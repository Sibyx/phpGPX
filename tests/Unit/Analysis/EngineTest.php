<?php

namespace phpGPX\Tests\Unit\Analysis;

use phpGPX\Analysis\AbstractPointAnalyzer;
use phpGPX\Analysis\BoundsAnalyzer;
use phpGPX\Analysis\DistanceAnalyzer;
use phpGPX\Analysis\ElevationAnalyzer;
use phpGPX\Analysis\MovementAnalyzer;
use phpGPX\Analysis\PointAnalyzerInterface;
use phpGPX\Analysis\Engine;
use phpGPX\Analysis\TrackPointExtensionAnalyzer;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;
use phpGPX\Models\Route;
use phpGPX\Models\Segment;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
	private function makePoint(
		float $lat,
		float $lon,
		?float $ele = null,
		?string $time = null
	): Point {
		$p = new Point(PointType::Trackpoint);
		$p->latitude = $lat;
		$p->longitude = $lon;
		$p->elevation = $ele;
		$p->time = $time ? new \DateTime($time) : null;
		return $p;
	}

	public function testAnalyzersCalledInOrder(): void
	{
		$order = [];

		$a1 = new class($order) extends AbstractPointAnalyzer {
			public function __construct(private array &$order) {}
			public function begin(): void { $this->order[] = 'a1:begin'; }
			public function visit(Point $current, ?Point $previous): void { $this->order[] = 'a1:visit'; }
			public function end(Stats $stats): void { $this->order[] = 'a1:end'; }
		};

		$a2 = new class($order) extends AbstractPointAnalyzer {
			public function __construct(private array &$order) {}
			public function begin(): void { $this->order[] = 'a2:begin'; }
			public function visit(Point $current, ?Point $previous): void { $this->order[] = 'a2:visit'; }
			public function end(Stats $stats): void { $this->order[] = 'a2:end'; }
		};

		$engine = (new Engine())->addAnalyzer($a1)->addAnalyzer($a2);

		$segment = new Segment();
		$segment->points = [$this->makePoint(48.0, 17.0)];
		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$engine->process($gpx);

		$this->assertSame([
			'a1:begin', 'a2:begin',    // begin phase
			'a1:visit', 'a2:visit',    // visit phase (1 point)
			'a1:end', 'a2:end',        // end phase
		], $order);
	}

	public function testAddAnalyzerReturnsSelf(): void
	{
		$engine = new Engine();
		$result = $engine->addAnalyzer(new BoundsAnalyzer());
		$this->assertSame($engine, $result);
	}

	public function testDefaultFactoryCreatesFullEngine(): void
	{
		$engine = Engine::default();

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.0, 17.0, 200, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.001, 17.001, 210, '2024-01-01T10:00:10Z'),
			$this->makePoint(48.002, 17.002, 220, '2024-01-01T10:00:20Z'),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$stats = $result->tracks[0]->segments[0]->stats;

		// Distance
		$this->assertGreaterThan(0, $stats->distance);
		$this->assertGreaterThan(0, $stats->realDistance);

		// Elevation
		$this->assertGreaterThan(0, $stats->cumulativeElevationGain);

		// Altitude
		$this->assertEqualsWithDelta(200.0, $stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(220.0, $stats->maxAltitude, 0.01);

		// Timestamps
		$this->assertNotNull($stats->startedAt);
		$this->assertNotNull($stats->finishedAt);

		// Duration (derived by engine)
		$this->assertEqualsWithDelta(20.0, $stats->duration, 0.1);

		// Speed (derived by engine)
		$this->assertNotNull($stats->averageSpeed);

		// Bounds
		$this->assertNotNull($stats->bounds);

		// Movement
		$this->assertNotNull($stats->movingDuration);

		// Metadata bounds (from BoundsAnalyzer::finalizeFile)
		$this->assertNotNull($result->metadata);
		$this->assertNotNull($result->metadata->bounds);
	}

	public function testEngineCreatesStatsForEmptySegment(): void
	{
		$engine = Engine::default();

		$segment = new Segment();
		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$this->assertInstanceOf(Stats::class, $result->tracks[0]->segments[0]->stats);
		$this->assertNull($result->tracks[0]->segments[0]->stats->distance);
	}

	public function testEngineCreatesStatsForEmptyTrack(): void
	{
		$engine = Engine::default();

		$track = new Track();
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$this->assertInstanceOf(Stats::class, $result->tracks[0]->stats);
	}

	public function testEngineProcessesRoutes(): void
	{
		$engine = Engine::default();

		$route = new Route();
		$route->points = [
			$this->makePoint(48.0, 17.0, 100),
			$this->makePoint(48.001, 17.001, 110),
		];

		$gpx = new GpxFile();
		$gpx->routes = [$route];

		$result = $engine->process($gpx);

		$this->assertNotNull($result->routes[0]->stats);
		$this->assertGreaterThan(0, $result->routes[0]->stats->distance);
	}

	public function testDerivedStatsComputedAfterAnalyzers(): void
	{
		$engine = Engine::default();

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.0, 17.0, 100, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.001, 17.001, 110, '2024-01-01T10:01:00Z'),
		];

		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$stats = $result->tracks[0]->segments[0]->stats;

		// Speed = distance / duration
		$expectedSpeed = $stats->distance / $stats->duration;
		$this->assertEqualsWithDelta($expectedSpeed, $stats->averageSpeed, 0.001);

		// Pace = duration / (distance / 1000)
		$expectedPace = $stats->duration / ($stats->distance / 1000);
		$this->assertEqualsWithDelta($expectedPace, $stats->averagePace, 0.001);
	}

	public function testNoAnalyzersStillWorks(): void
	{
		$engine = new Engine();

		$segment = new Segment();
		$segment->points = [$this->makePoint(48.0, 17.0)];
		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$this->assertInstanceOf(Stats::class, $result->tracks[0]->segments[0]->stats);
	}

	public function testSortByTimestampSortsTrackPoints(): void
	{
		$engine = Engine::default(sortByTimestamp: true);

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.002, 17.0, 100, '2024-01-01T10:00:20Z'),
			$this->makePoint(48.000, 17.0, 100, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.001, 17.0, 100, '2024-01-01T10:00:10Z'),
		];

		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$points = $result->tracks[0]->segments[0]->points;
		$this->assertEqualsWithDelta(48.000, $points[0]->latitude, 0.001);
		$this->assertEqualsWithDelta(48.001, $points[1]->latitude, 0.001);
		$this->assertEqualsWithDelta(48.002, $points[2]->latitude, 0.001);
	}

	public function testSortByTimestampSortsRoutePoints(): void
	{
		$engine = Engine::default(sortByTimestamp: true);

		$p1 = new Point(PointType::Routepoint);
		$p1->latitude = 48.002;
		$p1->longitude = 17.0;
		$p1->time = new \DateTime('2024-01-01T10:00:20Z');

		$p2 = new Point(PointType::Routepoint);
		$p2->latitude = 48.000;
		$p2->longitude = 17.0;
		$p2->time = new \DateTime('2024-01-01T10:00:00Z');

		$route = new Route();
		$route->points = [$p1, $p2];
		$gpx = new GpxFile();
		$gpx->routes = [$route];

		$result = $engine->process($gpx);

		$this->assertEqualsWithDelta(48.000, $result->routes[0]->points[0]->latitude, 0.001);
		$this->assertEqualsWithDelta(48.002, $result->routes[0]->points[1]->latitude, 0.001);
	}

	public function testSortByTimestampSkipsPointsWithoutTimestamps(): void
	{
		$engine = Engine::default(sortByTimestamp: true);

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.002, 17.0, 100),
			$this->makePoint(48.000, 17.0, 100),
		];

		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		// Order should be unchanged
		$this->assertEqualsWithDelta(48.002, $result->tracks[0]->segments[0]->points[0]->latitude, 0.001);
		$this->assertEqualsWithDelta(48.000, $result->tracks[0]->segments[0]->points[1]->latitude, 0.001);
	}

	public function testDefaultFactoryCustomParameters(): void
	{
		$engine = Engine::default(
			applyElevationSmoothing: true,
			elevationSmoothingThreshold: 5,
			speedThreshold: 1.0,
		);

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.0, 17.0, 100, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.001, 17.001, 103, '2024-01-01T10:00:10Z'),
			$this->makePoint(48.002, 17.002, 120, '2024-01-01T10:00:20Z'),
		];

		$track = new Track();
		$track->segments = [$segment];
		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$stats = $result->tracks[0]->segments[0]->stats;
		// With smoothing threshold 5, the 3m gain (100→103) is ignored
		// Only the 17m gain (103→120) counts
		$this->assertEqualsWithDelta(20.0, $stats->cumulativeElevationGain, 0.01);
	}
}