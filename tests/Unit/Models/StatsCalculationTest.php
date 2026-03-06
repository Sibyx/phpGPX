<?php

namespace phpGPX\Tests\Unit\Models;

use phpGPX\Models\Point;
use phpGPX\Models\Route;
use phpGPX\Models\Segment;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;
use phpGPX\phpGPX;
use PHPUnit\Framework\TestCase;

class StatsCalculationTest extends TestCase
{
	protected function setUp(): void
	{
		phpGPX::$CALCULATE_STATS = true;
		phpGPX::$IGNORE_ELEVATION_0 = false;
		phpGPX::$APPLY_DISTANCE_SMOOTHING = false;
		phpGPX::$APPLY_ELEVATION_SMOOTHING = false;
	}

	private function makePoint(
		float $lat,
		float $lon,
		?float $ele = null,
		?string $time = null
	): Point {
		$p = new Point(Point::TRACKPOINT);
		$p->latitude = $lat;
		$p->longitude = $lon;
		$p->elevation = $ele;
		$p->time = $time ? new \DateTime($time) : null;
		return $p;
	}

	private function makeRoutePoint(
		float $lat,
		float $lon,
		?float $ele = null,
		?string $time = null
	): Point {
		$p = new Point(Point::ROUTEPOINT);
		$p->latitude = $lat;
		$p->longitude = $lon;
		$p->elevation = $ele;
		$p->time = $time ? new \DateTime($time) : null;
		return $p;
	}

	// --- Segment stats ---

	public function testSegmentStatsEmptyPoints(): void
	{
		$segment = new Segment();
		$segment->recalculateStats();

		$this->assertInstanceOf(Stats::class, $segment->stats);
		$this->assertNull($segment->stats->distance);
	}

	public function testSegmentStatsSinglePoint(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 2419, '2017-08-13T07:10:41Z'),
		];
		$segment->recalculateStats();

		$this->assertEqualsWithDelta(0.0, $segment->stats->distance, 0.01);
		$this->assertEqualsWithDelta(0.0, $segment->stats->cumulativeElevationGain, 0.01);
		$this->assertEqualsWithDelta(0.0, $segment->stats->cumulativeElevationLoss, 0.01);
		$this->assertEquals(46.571948, $segment->stats->startedAtCoords['lat']);
		$this->assertEquals(46.571948, $segment->stats->finishedAtCoords['lat']);
	}

	public function testSegmentStatsBasicTrack(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 2419, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, 2418.88, '2017-08-13T07:10:54Z'),
			$this->makePoint(46.572088, 8.414911, 2419.90, '2017-08-13T07:11:56Z'),
			$this->makePoint(46.572069, 8.414912, 2422, '2017-08-13T07:12:15Z'),
			$this->makePoint(46.572054, 8.414888, 2425, '2017-08-13T07:12:18Z'),
		];
		$segment->recalculateStats();

		// Distance should be positive
		$this->assertGreaterThan(0, $segment->stats->distance);
		$this->assertGreaterThan(0, $segment->stats->realDistance);

		// Elevation gain: 2418.88→2419.90 (+1.02), 2419.90→2422 (+2.1), 2422→2425 (+3)
		$this->assertGreaterThan(6, $segment->stats->cumulativeElevationGain);

		// Elevation loss: 2419→2418.88 (-0.12)
		$this->assertGreaterThan(0, $segment->stats->cumulativeElevationLoss);

		// Duration
		$this->assertEqualsWithDelta(97.0, $segment->stats->duration, 0.1);

		// Speed and pace
		$this->assertNotNull($segment->stats->averageSpeed);
		$this->assertGreaterThan(0, $segment->stats->averageSpeed);
		$this->assertNotNull($segment->stats->averagePace);
		$this->assertGreaterThan(0, $segment->stats->averagePace);

		// Altitude bounds
		$this->assertEqualsWithDelta(2418.88, $segment->stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(2425, $segment->stats->maxAltitude, 0.01);

		// Start/end coordinates
		$this->assertEquals(46.571948, $segment->stats->startedAtCoords['lat']);
		$this->assertEquals(46.572054, $segment->stats->finishedAtCoords['lat']);
	}

	public function testSegmentStatsWithoutTimestamps(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 100),
			$this->makePoint(46.572016, 8.414866, 200),
		];
		$segment->recalculateStats();

		// Distance should still be calculated
		$this->assertGreaterThan(0, $segment->stats->distance);

		// Duration, speed, pace should be null (no timestamps)
		$this->assertNull($segment->stats->duration);
		$this->assertNull($segment->stats->averageSpeed);
		$this->assertNull($segment->stats->averagePace);
	}

	public function testSegmentStatsWithoutElevation(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, null, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, null, '2017-08-13T07:10:54Z'),
		];
		$segment->recalculateStats();

		$this->assertGreaterThan(0, $segment->stats->distance);
		$this->assertEqualsWithDelta(0.0, $segment->stats->cumulativeElevationGain, 0.001);
		$this->assertEqualsWithDelta(0.0, $segment->stats->cumulativeElevationLoss, 0.001);
	}

	public function testSegmentStatsIgnoreElevationZero(): void
	{
		phpGPX::$IGNORE_ELEVATION_0 = true;

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 100, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, 0, '2017-08-13T07:10:54Z'),
			$this->makePoint(46.572088, 8.414911, 200, '2017-08-13T07:11:56Z'),
		];
		$segment->recalculateStats();

		// minAltitude should NOT be 0 when IGNORE_ELEVATION_0 is true
		$this->assertGreaterThan(0, $segment->stats->minAltitude);
	}

	public function testSegmentStatsRecalculateResetsValues(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 100, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, 200, '2017-08-13T07:10:54Z'),
		];
		$segment->recalculateStats();
		$firstDistance = $segment->stats->distance;

		// Recalculate again — should get same result (not accumulated)
		$segment->recalculateStats();
		$this->assertEqualsWithDelta($firstDistance, $segment->stats->distance, 0.001);
	}

	// --- Track stats ---

	public function testTrackStatsEmptySegments(): void
	{
		$track = new Track();
		$track->recalculateStats();

		$this->assertInstanceOf(Stats::class, $track->stats);
		$this->assertNull($track->stats->distance);
	}

	public function testTrackStatsSingleSegment(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 2419, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, 2425, '2017-08-13T07:10:54Z'),
		];

		$track = new Track();
		$track->segments = [$segment];
		$track->recalculateStats();

		$this->assertGreaterThan(0, $track->stats->distance);
		$this->assertEqualsWithDelta(6.0, $track->stats->cumulativeElevationGain, 0.01);
		$this->assertEqualsWithDelta(2419.0, $track->stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(2425.0, $track->stats->maxAltitude, 0.01);
	}

	public function testTrackStatsMultipleSegmentsAggregated(): void
	{
		$seg1 = new Segment();
		$seg1->points = [
			$this->makePoint(46.571948, 8.414757, 100, '2017-08-13T07:10:00Z'),
			$this->makePoint(46.572016, 8.414866, 150, '2017-08-13T07:10:30Z'),
		];

		$seg2 = new Segment();
		$seg2->points = [
			$this->makePoint(46.573000, 8.415000, 200, '2017-08-13T07:15:00Z'),
			$this->makePoint(46.574000, 8.416000, 180, '2017-08-13T07:15:30Z'),
		];

		$track = new Track();
		$track->segments = [$seg1, $seg2];
		$track->recalculateStats();

		// Distances should be summed across segments
		$seg1->recalculateStats();
		$seg2->recalculateStats();
		$expectedDistance = $seg1->stats->distance + $seg2->stats->distance;
		$this->assertEqualsWithDelta($expectedDistance, $track->stats->distance, 0.01);

		// Elevation gain aggregated: seg1 has 50m gain, seg2 has 0
		$this->assertEqualsWithDelta(50.0, $track->stats->cumulativeElevationGain, 0.01);

		// Elevation loss aggregated: seg1 has 0, seg2 has 20m loss
		$this->assertEqualsWithDelta(20.0, $track->stats->cumulativeElevationLoss, 0.01);

		// Min altitude should be minimum across all segments
		$this->assertEqualsWithDelta(100.0, $track->stats->minAltitude, 0.01);

		// Max altitude should be maximum across all segments
		$this->assertEqualsWithDelta(200.0, $track->stats->maxAltitude, 0.01);

		// Start/end should span the entire track
		$this->assertNotNull($track->stats->startedAt);
		$this->assertNotNull($track->stats->finishedAt);

		// Duration spans first point of first segment to last point of last segment
		$this->assertEqualsWithDelta(330.0, $track->stats->duration, 0.1);
	}

	public function testTrackGetPointsFlattensSegments(): void
	{
		$seg1 = new Segment();
		$seg1->points = [
			$this->makePoint(46.571948, 8.414757),
			$this->makePoint(46.572016, 8.414866),
		];

		$seg2 = new Segment();
		$seg2->points = [
			$this->makePoint(46.573000, 8.415000),
		];

		$track = new Track();
		$track->segments = [$seg1, $seg2];

		$allPoints = $track->getPoints();
		$this->assertCount(3, $allPoints);
	}

	// --- Route stats ---

	public function testRouteStatsEmptyPoints(): void
	{
		$route = new Route();
		$route->recalculateStats();

		$this->assertInstanceOf(Stats::class, $route->stats);
		$this->assertNull($route->stats->distance);
	}

	public function testRouteStatsBasic(): void
	{
		$route = new Route();
		$route->points = [
			$this->makeRoutePoint(54.9328621088893, 9.860624216140083, 0.0),
			$this->makeRoutePoint(54.93293237320851, 9.86092208681491, 1.0),
			$this->makeRoutePoint(54.93327743521187, 9.86187816543752, 2.0),
			$this->makeRoutePoint(54.93342326167919, 9.862439849679859, 3.0),
		];
		$route->recalculateStats();

		$this->assertGreaterThan(0, $route->stats->distance);
		$this->assertEqualsWithDelta(3.0, $route->stats->cumulativeElevationGain, 0.01);
		$this->assertEqualsWithDelta(0.0, $route->stats->cumulativeElevationLoss, 0.01);
		$this->assertEqualsWithDelta(0.0, $route->stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(3.0, $route->stats->maxAltitude, 0.01);
	}

	// --- Stats model ---

	public function testStatsReset(): void
	{
		$stats = new Stats();
		$stats->distance = 100.0;
		$stats->duration = 60.0;
		$stats->averageSpeed = 1.67;
		$stats->cumulativeElevationGain = 50.0;

		$stats->reset();

		$this->assertNull($stats->distance);
		$this->assertNull($stats->duration);
		$this->assertNull($stats->averageSpeed);
		$this->assertNull($stats->cumulativeElevationGain);
		$this->assertNull($stats->startedAt);
		$this->assertNull($stats->finishedAt);
	}

	public function testStatsToArray(): void
	{
		$stats = new Stats();
		$stats->distance = 1000.0;
		$stats->realDistance = 1005.0;
		$stats->averageSpeed = 2.5;
		$stats->averagePace = 400.0;
		$stats->minAltitude = 100.0;
		$stats->maxAltitude = 200.0;
		$stats->duration = 400.0;

		$array = $stats->toArray();

		$this->assertEquals(1000.0, $array['distance']);
		$this->assertEquals(1005.0, $array['realDistance']);
		$this->assertEquals(2.5, $array['avgSpeed']);
		$this->assertEquals(400.0, $array['avgPace']);
		$this->assertEquals(100.0, $array['minAltitude']);
		$this->assertEquals(200.0, $array['maxAltitude']);
		$this->assertEquals(400.0, $array['duration']);
	}

	public function testStatsJsonSerialize(): void
	{
		$stats = new Stats();
		$stats->distance = 500.0;

		$json = $stats->jsonSerialize();
		$this->assertEquals($stats->toArray(), $json);
	}
}