<?php

namespace phpGPX\Tests\Unit\Models;

use phpGPX\Analysis\AltitudeAnalyzer;
use phpGPX\Analysis\DistanceAnalyzer;
use phpGPX\Analysis\ElevationAnalyzer;
use phpGPX\Analysis\Engine;
use phpGPX\Analysis\TimestampAnalyzer;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;
use phpGPX\Models\Route;
use phpGPX\Models\Segment;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;
use PHPUnit\Framework\TestCase;

class StatsCalculationTest extends TestCase
{
	private Engine $engine;

	protected function setUp(): void
	{
		$this->engine = (new Engine())
			->addAnalyzer(new DistanceAnalyzer())
			->addAnalyzer(new ElevationAnalyzer())
			->addAnalyzer(new AltitudeAnalyzer())
			->addAnalyzer(new TimestampAnalyzer());
	}

	private function makePoint(
		float $lat,
		float $lon,
		?float $ele = null,
		?string $time = null,
	): Point {
		$p = new Point(PointType::Trackpoint);
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
		?string $time = null,
	): Point {
		$p = new Point(PointType::Routepoint);
		$p->latitude = $lat;
		$p->longitude = $lon;
		$p->elevation = $ele;
		$p->time = $time ? new \DateTime($time) : null;
		return $p;
	}

	private function processTrack(Track $track): GpxFile
	{
		$gpx = new GpxFile();
		$gpx->tracks = [$track];
		return $this->engine->process($gpx);
	}

	private function processRoute(Route $route): GpxFile
	{
		$gpx = new GpxFile();
		$gpx->routes = [$route];
		return $this->engine->process($gpx);
	}

	private function processSegment(Segment $segment): GpxFile
	{
		$track = new Track();
		$track->segments = [$segment];
		return $this->processTrack($track);
	}

	// --- Segment stats ---

	public function testSegmentStatsEmptyPoints(): void
	{
		$segment = new Segment();
		$result = $this->processSegment($segment);

		$stats = $result->tracks[0]->segments[0]->stats;
		$this->assertInstanceOf(Stats::class, $stats);
		$this->assertNull($stats->distance);
	}

	public function testSegmentStatsSinglePoint(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 2419, '2017-08-13T07:10:41Z'),
		];
		$result = $this->processSegment($segment);
		$stats = $result->tracks[0]->segments[0]->stats;

		$this->assertEqualsWithDelta(0.0, $stats->distance, 0.01);
		$this->assertEqualsWithDelta(0.0, $stats->cumulativeElevationGain, 0.01);
		$this->assertEqualsWithDelta(0.0, $stats->cumulativeElevationLoss, 0.01);
		$this->assertEquals(46.571948, $stats->startedAtCoords['lat']);
		$this->assertEquals(46.571948, $stats->finishedAtCoords['lat']);
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
		$result = $this->processSegment($segment);
		$stats = $result->tracks[0]->segments[0]->stats;

		// Distance should be positive
		$this->assertGreaterThan(0, $stats->distance);
		$this->assertGreaterThan(0, $stats->realDistance);

		// Elevation gain: 2418.88→2419.90 (+1.02), 2419.90→2422 (+2.1), 2422→2425 (+3)
		$this->assertGreaterThan(6, $stats->cumulativeElevationGain);

		// Elevation loss: 2419→2418.88 (-0.12)
		$this->assertGreaterThan(0, $stats->cumulativeElevationLoss);

		// Duration
		$this->assertEqualsWithDelta(97.0, $stats->duration, 0.1);

		// Speed and pace
		$this->assertNotNull($stats->averageSpeed);
		$this->assertGreaterThan(0, $stats->averageSpeed);
		$this->assertNotNull($stats->averagePace);
		$this->assertGreaterThan(0, $stats->averagePace);

		// Altitude bounds
		$this->assertEqualsWithDelta(2418.88, $stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(2425, $stats->maxAltitude, 0.01);

		// Start/end coordinates
		$this->assertEquals(46.571948, $stats->startedAtCoords['lat']);
		$this->assertEquals(46.572054, $stats->finishedAtCoords['lat']);
	}

	public function testSegmentStatsWithoutTimestamps(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 100),
			$this->makePoint(46.572016, 8.414866, 200),
		];
		$result = $this->processSegment($segment);
		$stats = $result->tracks[0]->segments[0]->stats;

		// Distance should still be calculated
		$this->assertGreaterThan(0, $stats->distance);

		// Duration, speed, pace should be null (no timestamps)
		$this->assertNull($stats->duration);
		$this->assertNull($stats->averageSpeed);
		$this->assertNull($stats->averagePace);
	}

	public function testSegmentStatsWithoutElevation(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, null, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, null, '2017-08-13T07:10:54Z'),
		];
		$result = $this->processSegment($segment);
		$stats = $result->tracks[0]->segments[0]->stats;

		$this->assertGreaterThan(0, $stats->distance);
		$this->assertEqualsWithDelta(0.0, $stats->cumulativeElevationGain, 0.001);
		$this->assertEqualsWithDelta(0.0, $stats->cumulativeElevationLoss, 0.001);
	}

	public function testSegmentStatsIgnoreElevationZero(): void
	{
		$engine = (new Engine())
			->addAnalyzer(new DistanceAnalyzer())
			->addAnalyzer(new ElevationAnalyzer(ignoreZeroElevation: true))
			->addAnalyzer(new AltitudeAnalyzer(ignoreZeroElevation: true))
			->addAnalyzer(new TimestampAnalyzer());

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 100, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, 0, '2017-08-13T07:10:54Z'),
			$this->makePoint(46.572088, 8.414911, 200, '2017-08-13T07:11:56Z'),
		];

		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];
		$result = $engine->process($gpx);

		// minAltitude should NOT be 0 when ignoreZeroElevation is true
		$this->assertGreaterThan(0, $result->tracks[0]->segments[0]->stats->minAltitude);
	}

	public function testSegmentStatsRecalculateResetsValues(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(46.571948, 8.414757, 100, '2017-08-13T07:10:41Z'),
			$this->makePoint(46.572016, 8.414866, 200, '2017-08-13T07:10:54Z'),
		];
		$result = $this->processSegment($segment);
		$firstDistance = $result->tracks[0]->segments[0]->stats->distance;

		// Process again — should get same result (not accumulated)
		$result2 = $this->processSegment($segment);
		$this->assertEqualsWithDelta($firstDistance, $result2->tracks[0]->segments[0]->stats->distance, 0.001);
	}

	// --- Track stats ---

	public function testTrackStatsEmptySegments(): void
	{
		$track = new Track();
		$result = $this->processTrack($track);

		$this->assertInstanceOf(Stats::class, $result->tracks[0]->stats);
		$this->assertNull($result->tracks[0]->stats->distance);
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
		$result = $this->processTrack($track);
		$stats = $result->tracks[0]->stats;

		$this->assertGreaterThan(0, $stats->distance);
		$this->assertEqualsWithDelta(6.0, $stats->cumulativeElevationGain, 0.01);
		$this->assertEqualsWithDelta(2419.0, $stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(2425.0, $stats->maxAltitude, 0.01);
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
		$result = $this->processTrack($track);
		$stats = $result->tracks[0]->stats;

		// Get individual segment distances for comparison
		$seg1Distance = $result->tracks[0]->segments[0]->stats->distance;
		$seg2Distance = $result->tracks[0]->segments[1]->stats->distance;
		$expectedDistance = $seg1Distance + $seg2Distance;
		$this->assertEqualsWithDelta($expectedDistance, $stats->distance, 0.01);

		// Elevation gain aggregated: seg1 has 50m gain, seg2 has 0
		$this->assertEqualsWithDelta(50.0, $stats->cumulativeElevationGain, 0.01);

		// Elevation loss aggregated: seg1 has 0, seg2 has 20m loss
		$this->assertEqualsWithDelta(20.0, $stats->cumulativeElevationLoss, 0.01);

		// Min altitude should be minimum across all segments
		$this->assertEqualsWithDelta(100.0, $stats->minAltitude, 0.01);

		// Max altitude should be maximum across all segments
		$this->assertEqualsWithDelta(200.0, $stats->maxAltitude, 0.01);

		// Start/end should span the entire track
		$this->assertNotNull($stats->startedAt);
		$this->assertNotNull($stats->finishedAt);

		// Duration spans first point of first segment to last point of last segment
		$this->assertEqualsWithDelta(330.0, $stats->duration, 0.1);
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
		$result = $this->processRoute($route);

		$this->assertInstanceOf(Stats::class, $result->routes[0]->stats);
		$this->assertNull($result->routes[0]->stats->distance);
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
		$result = $this->processRoute($route);
		$stats = $result->routes[0]->stats;

		$this->assertGreaterThan(0, $stats->distance);
		$this->assertEqualsWithDelta(3.0, $stats->cumulativeElevationGain, 0.01);
		$this->assertEqualsWithDelta(0.0, $stats->cumulativeElevationLoss, 0.01);
		$this->assertEqualsWithDelta(0.0, $stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(3.0, $stats->maxAltitude, 0.01);
	}

	// --- Stats model ---

	public function testStatsJsonSerialize(): void
	{
		$stats = new Stats();
		$stats->distance = 1000.0;
		$stats->realDistance = 1005.0;
		$stats->averageSpeed = 2.5;
		$stats->averagePace = 400.0;
		$stats->minAltitude = 100.0;
		$stats->maxAltitude = 200.0;
		$stats->duration = 400.0;

		$json = $stats->jsonSerialize();

		$this->assertEquals(1000.0, $json['distance']);
		$this->assertEquals(1005.0, $json['realDistance']);
		$this->assertEquals(2.5, $json['avgSpeed']);
		$this->assertEquals(400.0, $json['avgPace']);
		$this->assertEquals(100.0, $json['minAltitude']);
		$this->assertEquals(200.0, $json['maxAltitude']);
		$this->assertEquals(400.0, $json['duration']);
	}
}
