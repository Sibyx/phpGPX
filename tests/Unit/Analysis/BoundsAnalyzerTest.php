<?php

namespace phpGPX\Tests\Unit\Analysis;

use phpGPX\Analysis\BoundsAnalyzer;
use phpGPX\Analysis\Engine;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\Route;
use phpGPX\Models\Segment;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;
use PHPUnit\Framework\TestCase;

class BoundsAnalyzerTest extends TestCase
{
	private Engine $engine;

	protected function setUp(): void
	{
		$this->engine = (new Engine())->addAnalyzer(new BoundsAnalyzer());
	}

	private function makePoint(float $lat, float $lon): Point
	{
		$p = new Point(Point::TRACKPOINT);
		$p->latitude = $lat;
		$p->longitude = $lon;
		return $p;
	}

	public function testSegmentBounds(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.0, 17.0),
			$this->makePoint(49.0, 18.0),
			$this->makePoint(47.5, 16.5),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$bounds = $result->tracks[0]->segments[0]->stats->bounds;
		$this->assertNotNull($bounds);
		$this->assertEqualsWithDelta(47.5, $bounds->minLatitude, 0.001);
		$this->assertEqualsWithDelta(49.0, $bounds->maxLatitude, 0.001);
		$this->assertEqualsWithDelta(16.5, $bounds->minLongitude, 0.001);
		$this->assertEqualsWithDelta(18.0, $bounds->maxLongitude, 0.001);
	}

	public function testTrackBoundsAggregatesSegments(): void
	{
		$seg1 = new Segment();
		$seg1->points = [
			$this->makePoint(48.0, 17.0),
			$this->makePoint(49.0, 18.0),
		];

		$seg2 = new Segment();
		$seg2->points = [
			$this->makePoint(47.0, 16.0),
			$this->makePoint(48.5, 17.5),
		];

		$track = new Track();
		$track->segments = [$seg1, $seg2];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$trackBounds = $result->tracks[0]->stats->bounds;
		$this->assertNotNull($trackBounds);
		$this->assertEqualsWithDelta(47.0, $trackBounds->minLatitude, 0.001);
		$this->assertEqualsWithDelta(49.0, $trackBounds->maxLatitude, 0.001);
	}

	public function testRouteBounds(): void
	{
		$route = new Route();
		$route->points = [
			$this->makePoint(50.0, 14.0),
			$this->makePoint(51.0, 15.0),
		];

		$gpx = new GpxFile();
		$gpx->routes = [$route];

		$result = $this->engine->process($gpx);

		$this->assertNotNull($result->routes[0]->stats->bounds);
		$this->assertEqualsWithDelta(50.0, $result->routes[0]->stats->bounds->minLatitude, 0.001);
	}

	public function testMetadataBoundsSet(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.0, 17.0),
			$this->makePoint(49.0, 18.0),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$this->assertNotNull($result->metadata);
		$this->assertNotNull($result->metadata->bounds);
		$this->assertEqualsWithDelta(48.0, $result->metadata->bounds->minLatitude, 0.001);
	}

	public function testEmptyPointsNoBounds(): void
	{
		$segment = new Segment();
		$segment->points = [];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$this->assertNull($result->tracks[0]->segments[0]->stats->bounds);
	}

	public function testMetadataBoundsIncludesWaypoints(): void
	{
		$waypoint = new Point(Point::WAYPOINT);
		$waypoint->latitude = 50.0;
		$waypoint->longitude = 20.0;

		$segment = new Segment();
		$segment->points = [
			$this->makePoint(48.0, 17.0),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];
		$gpx->waypoints = [$waypoint];

		$result = $this->engine->process($gpx);

		$bounds = $result->metadata->bounds;
		$this->assertNotNull($bounds);
		$this->assertEqualsWithDelta(48.0, $bounds->minLatitude, 0.001);
		$this->assertEqualsWithDelta(50.0, $bounds->maxLatitude, 0.001);
		$this->assertEqualsWithDelta(17.0, $bounds->minLongitude, 0.001);
		$this->assertEqualsWithDelta(20.0, $bounds->maxLongitude, 0.001);
	}
}