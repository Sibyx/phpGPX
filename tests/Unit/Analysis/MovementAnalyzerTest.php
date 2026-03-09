<?php

namespace phpGPX\Tests\Unit\Analysis;

use phpGPX\Analysis\DistanceAnalyzer;
use phpGPX\Analysis\Engine;
use phpGPX\Analysis\MovementAnalyzer;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;
use phpGPX\Models\Route;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use PHPUnit\Framework\TestCase;

class MovementAnalyzerTest extends TestCase
{
	private function makePoint(float $lat, float $lon, string $time): Point
	{
		$p = new Point(PointType::Trackpoint);
		$p->latitude = $lat;
		$p->longitude = $lon;
		$p->time = new \DateTime($time);
		return $p;
	}

	private function makeEngine(float $speedThreshold = 0.1): Engine
	{
		return (new Engine())
			->addAnalyzer(new DistanceAnalyzer())
			->addAnalyzer(new MovementAnalyzer(speedThreshold: $speedThreshold));
	}

	public function testMovingDurationCalculated(): void
	{
		$engine = $this->makeEngine(speedThreshold: 0.1);

		$segment = new Segment();
		// Points ~111m apart (0.001 degrees lat) with 10s intervals = ~11 m/s
		$segment->points = [
			$this->makePoint(48.000, 17.0, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.001, 17.0, '2024-01-01T10:00:10Z'),
			$this->makePoint(48.002, 17.0, '2024-01-01T10:00:20Z'),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$segStats = $result->tracks[0]->segments[0]->stats;
		$this->assertNotNull($segStats->movingDuration);
		$this->assertEqualsWithDelta(20.0, $segStats->movingDuration, 0.001);
		$this->assertNotNull($segStats->movingAverageSpeed);
	}

	public function testStoppedPointsExcluded(): void
	{
		$engine = $this->makeEngine(speedThreshold: 0.5);

		$segment = new Segment();
		// First two points: same location, 60s apart = 0 m/s (stopped)
		// Second to third: ~111m, 10s = ~11 m/s (moving)
		$segment->points = [
			$this->makePoint(48.000, 17.0, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.000, 17.0, '2024-01-01T10:01:00Z'),
			$this->makePoint(48.001, 17.0, '2024-01-01T10:01:10Z'),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$segStats = $result->tracks[0]->segments[0]->stats;
		// Only the moving segment (10s) should count
		$this->assertEqualsWithDelta(10.0, $segStats->movingDuration, 0.001);
	}

	public function testTrackAggregatesSegments(): void
	{
		$engine = $this->makeEngine(speedThreshold: 0.1);

		$seg1 = new Segment();
		$seg1->points = [
			$this->makePoint(48.000, 17.0, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.001, 17.0, '2024-01-01T10:00:10Z'),
		];

		$seg2 = new Segment();
		$seg2->points = [
			$this->makePoint(48.002, 17.0, '2024-01-01T11:00:00Z'),
			$this->makePoint(48.003, 17.0, '2024-01-01T11:00:20Z'),
		];

		$track = new Track();
		$track->segments = [$seg1, $seg2];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$trackStats = $result->tracks[0]->stats;
		$this->assertEqualsWithDelta(30.0, $trackStats->movingDuration, 0.001);
		$this->assertNotNull($trackStats->movingAverageSpeed);
	}

	public function testRouteDuration(): void
	{
		$engine = $this->makeEngine(speedThreshold: 0.1);

		$route = new Route();
		$route->points = [
			$this->makePoint(48.000, 17.0, '2024-01-01T10:00:00Z'),
			$this->makePoint(48.001, 17.0, '2024-01-01T10:00:15Z'),
		];

		$gpx = new GpxFile();
		$gpx->routes = [$route];

		$result = $engine->process($gpx);

		$this->assertNotNull($result->routes[0]->stats->movingDuration);
		$this->assertEqualsWithDelta(15.0, $result->routes[0]->stats->movingDuration, 0.001);
	}

	public function testNoTimestampsReturnsNull(): void
	{
		$engine = $this->makeEngine();

		$p1 = new Point(PointType::Trackpoint);
		$p1->latitude = 48.0;
		$p1->longitude = 17.0;

		$p2 = new Point(PointType::Trackpoint);
		$p2->latitude = 48.1;
		$p2->longitude = 17.1;

		$segment = new Segment();
		$segment->points = [$p1, $p2];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$this->assertNull($result->tracks[0]->segments[0]->stats->movingDuration);
	}

	public function testSinglePointReturnsNull(): void
	{
		$engine = $this->makeEngine();

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.0, 17.0, '2024-01-01T10:00:00Z'),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $engine->process($gpx);

		$this->assertNull($result->tracks[0]->segments[0]->stats->movingDuration);
	}
}
