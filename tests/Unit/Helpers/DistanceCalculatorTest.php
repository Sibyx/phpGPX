<?php

namespace phpGPX\Tests\Unit\Helpers;

use phpGPX\Helpers\DistanceCalculator;
use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\Point;
use phpGPX\phpGPX;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
	protected function setUp(): void
	{
		phpGPX::$APPLY_DISTANCE_SMOOTHING = false;
	}

	private function makePoint(float $lat, float $lon, ?float $ele = null): Point
	{
		$p = new Point(Point::TRACKPOINT);
		$p->latitude = $lat;
		$p->longitude = $lon;
		$p->elevation = $ele;
		return $p;
	}

	public function testEmptyPoints(): void
	{
		$calc = new DistanceCalculator([]);
		$this->assertEqualsWithDelta(0.0, $calc->getRawDistance(), 0.001);
		$this->assertEqualsWithDelta(0.0, $calc->getRealDistance(), 0.001);
	}

	public function testSinglePoint(): void
	{
		$points = [$this->makePoint(48.157, 17.054)];
		$calc = new DistanceCalculator($points);
		$this->assertEqualsWithDelta(0.0, $calc->getRawDistance(), 0.001);
	}

	public function testTwoPoints(): void
	{
		$p1 = $this->makePoint(48.1573923225717, 17.0547121910204, 100);
		$p2 = $this->makePoint(48.1644916381763, 17.0591753907502, 200);

		$expectedRaw = GeoHelper::getRawDistance($p1, $p2);
		$expectedReal = GeoHelper::getRealDistance($p1, $p2);

		$calc = new DistanceCalculator([$p1, $p2]);

		$this->assertEqualsWithDelta($expectedRaw, $calc->getRawDistance(), 0.01);
		$this->assertEqualsWithDelta($expectedReal, $calc->getRealDistance(), 0.01);
	}

	public function testMultiplePointsAccumulate(): void
	{
		// Three points forming a path — distance should be sum of segments
		$p1 = $this->makePoint(46.571948, 8.414757, 2419);
		$p2 = $this->makePoint(46.572016, 8.414866, 2418.88);
		$p3 = $this->makePoint(46.572088, 8.414911, 2419.90);

		$d12 = GeoHelper::getRawDistance($p1, $p2);
		$d23 = GeoHelper::getRawDistance($p2, $p3);

		$calc = new DistanceCalculator([$p1, $p2, $p3]);
		$totalRaw = $calc->getRawDistance();

		$this->assertEqualsWithDelta($d12 + $d23, $totalRaw, 0.01);
	}

	public function testPointsDifferenceAndDistanceAreSet(): void
	{
		$p1 = $this->makePoint(46.571948, 8.414757);
		$p2 = $this->makePoint(46.572016, 8.414866);
		$p3 = $this->makePoint(46.572088, 8.414911);

		$calc = new DistanceCalculator([$p1, $p2, $p3]);
		$calc->getRawDistance();

		// First point should have no difference set
		$this->assertNull($p1->difference);

		// Second point should have difference from first
		$this->assertNotNull($p2->difference);
		$this->assertGreaterThan(0, $p2->difference);

		// Third point distance should be cumulative
		$this->assertNotNull($p3->distance);
		$this->assertEqualsWithDelta($p2->difference + $p3->difference, $p3->distance, 0.01);
	}

	public function testDistanceSmoothingFiltersSmallMovements(): void
	{
		phpGPX::$APPLY_DISTANCE_SMOOTHING = true;
		phpGPX::$DISTANCE_SMOOTHING_THRESHOLD = 10; // 10 meter threshold

		// Points very close together (< 10m apart)
		$p1 = $this->makePoint(46.571948, 8.414757);
		$p2 = $this->makePoint(46.571949, 8.414758); // ~0.1m away
		$p3 = $this->makePoint(46.571950, 8.414759); // ~0.1m away

		$calc = new DistanceCalculator([$p1, $p2, $p3]);
		$distance = $calc->getRawDistance();

		// With smoothing, these tiny movements should be filtered out
		$this->assertEqualsWithDelta(0.0, $distance, 0.01);

		phpGPX::$APPLY_DISTANCE_SMOOTHING = false;
	}

	public function testDistanceSmoothingKeepsLargeMovements(): void
	{
		phpGPX::$APPLY_DISTANCE_SMOOTHING = true;
		phpGPX::$DISTANCE_SMOOTHING_THRESHOLD = 2;

		// Points ~857m apart — well above threshold
		$p1 = $this->makePoint(48.1573923225717, 17.0547121910204);
		$p2 = $this->makePoint(48.1644916381763, 17.0591753907502);

		$calc = new DistanceCalculator([$p1, $p2]);
		$distance = $calc->getRawDistance();

		$this->assertGreaterThan(800, $distance);

		phpGPX::$APPLY_DISTANCE_SMOOTHING = false;
	}

	public function testSamePointRepeatedZeroDistance(): void
	{
		$p1 = $this->makePoint(46.571948, 8.414757);
		$p2 = $this->makePoint(46.571948, 8.414757);
		$p3 = $this->makePoint(46.571948, 8.414757);

		$calc = new DistanceCalculator([$p1, $p2, $p3]);
		$this->assertEqualsWithDelta(0.0, $calc->getRawDistance(), 0.001);
	}
}