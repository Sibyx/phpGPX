<?php

namespace phpGPX\Tests\Integration;

use phpGPX\phpGPX;
use PHPUnit\Framework\TestCase;

class GpxFileLoadTest extends TestCase
{
	private const FIXTURES_DIR = __DIR__ . '/../Fixtures';

	public function testLoadTimezeroGpx(): void
	{
		$gpxFile = phpGPX::load(self::FIXTURES_DIR . '/timezero.gpx');

		// Waypoints
		$this->assertCount(2, $gpxFile->waypoints);
		$this->assertEquals('Event 0000', $gpxFile->waypoints[0]->name);
		$this->assertEquals('Event 0001', $gpxFile->waypoints[1]->name);
		$this->assertEqualsWithDelta(49.3636333333086, $gpxFile->waypoints[0]->latitude, 0.0001);

		// Waypoint extensions (unsupported)
		$this->assertNotNull($gpxFile->waypoints[0]->extensions);
		$this->assertArrayHasKey('MxTimeZeroSymbol', $gpxFile->waypoints[0]->extensions->unsupported);

		// Tracks
		$this->assertCount(2, $gpxFile->tracks);
		$this->assertEquals('Ownship', $gpxFile->tracks[0]->name);

		// Track segments
		$this->assertCount(1, $gpxFile->tracks[0]->segments);
		$this->assertCount(3, $gpxFile->tracks[0]->segments[0]->points);

		// Track stats
		$this->assertNotNull($gpxFile->tracks[0]->stats);
		$this->assertGreaterThan(0, $gpxFile->tracks[0]->stats->distance);
		$this->assertEqualsWithDelta(2.31, $gpxFile->tracks[0]->stats->distance, 0.1);
		$this->assertEqualsWithDelta(9.0, $gpxFile->tracks[0]->stats->duration, 0.1);

		// Second track
		$this->assertCount(3, $gpxFile->tracks[1]->segments[0]->points);
		$this->assertEqualsWithDelta(7.06, $gpxFile->tracks[1]->stats->distance, 0.1);
		$this->assertEqualsWithDelta(3.0, $gpxFile->tracks[1]->stats->duration, 0.1);

		// XML generation should not throw
		$xml = $gpxFile->toXML()->saveXML();
		$this->assertNotEmpty($xml);
	}

	public function testLoadRouteGpx(): void
	{
		$gpxFile = phpGPX::load(self::FIXTURES_DIR . '/route.gpx');

		$this->assertEmpty($gpxFile->tracks);
		$this->assertEmpty($gpxFile->waypoints);
		$this->assertCount(2, $gpxFile->routes);

		$route1 = $gpxFile->routes[0];
		$this->assertEquals("Patrick's Route", $route1->name);
		$this->assertCount(4, $route1->points);

		// Route point coordinates
		$this->assertEqualsWithDelta(54.9328621088893, $route1->points[0]->latitude, 0.0001);
		$this->assertEqualsWithDelta(9.860624216140083, $route1->points[0]->longitude, 0.0001);

		// Route stats
		$this->assertNotNull($route1->stats);
		$this->assertGreaterThan(0, $route1->stats->distance);
		$this->assertEqualsWithDelta(0.0, $route1->stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(3.0, $route1->stats->maxAltitude, 0.01);

		// Second route
		$route2 = $gpxFile->routes[1];
		$this->assertEquals("Sibyx's Route", $route2->name);
		$this->assertCount(4, $route2->points);
	}

	public function testLoadGpsTrackGpx(): void
	{
		$gpxFile = phpGPX::load(self::FIXTURES_DIR . '/gps-track.gpx');

		$this->assertCount(1, $gpxFile->tracks);
		$this->assertEquals('GPS-Track', $gpxFile->tracks[0]->name);

		// First segment has 5 points, second segment is empty
		$track = $gpxFile->tracks[0];
		$this->assertGreaterThanOrEqual(1, count($track->segments));

		$segment = $track->segments[0];
		$this->assertCount(5, $segment->points);

		// Elevation data
		$this->assertEqualsWithDelta(2419, $segment->points[0]->elevation, 0.01);
		$this->assertEqualsWithDelta(2425, $segment->points[4]->elevation, 0.01);

		// Stats
		$this->assertNotNull($segment->stats);
		$this->assertEqualsWithDelta(2418.88, $segment->stats->minAltitude, 0.01);
		$this->assertEqualsWithDelta(2425, $segment->stats->maxAltitude, 0.01);
		$this->assertGreaterThan(0, $segment->stats->cumulativeElevationGain);
	}

	public function testLoadMinimalGpx(): void
	{
		$gpxFile = phpGPX::load(self::FIXTURES_DIR . '/minimal.gpx');

		// Has metadata
		$this->assertNotNull($gpxFile->metadata);
		$this->assertEquals('Minimal GPX Scenario', $gpxFile->metadata->name);
		$this->assertNotNull($gpxFile->metadata->author);
		$this->assertEquals('Jakub Dubec', $gpxFile->metadata->author->name);

		// Has route
		$this->assertCount(1, $gpxFile->routes);
		$this->assertEquals("Patrick's Route", $gpxFile->routes[0]->name);
		$this->assertCount(4, $gpxFile->routes[0]->points);

		// Has track with heart rate extensions
		$this->assertCount(1, $gpxFile->tracks);
		$this->assertEquals('Hike', $gpxFile->tracks[0]->name);
		$this->assertEquals('hiking', $gpxFile->tracks[0]->type);
		$this->assertCount(2, $gpxFile->tracks[0]->segments);

		// Check TrackPointExtension (heart rate)
		$firstPoint = $gpxFile->tracks[0]->segments[0]->points[0];
		$this->assertNotNull($firstPoint->extensions);
		$this->assertNotNull($firstPoint->extensions->trackPointExtension);
		$this->assertEqualsWithDelta(126, $firstPoint->extensions->trackPointExtension->hr, 0.1);
	}

	public function testLoadCreatorAttribute(): void
	{
		$gpxFile = phpGPX::load(self::FIXTURES_DIR . '/route.gpx');
		$this->assertEquals('RouteConverter', $gpxFile->creator);
	}

	public function testParseFromString(): void
	{
		$xml = file_get_contents(self::FIXTURES_DIR . '/route.gpx');
		$gpxFile = phpGPX::parse($xml);

		$this->assertCount(2, $gpxFile->routes);
		$this->assertEquals("Patrick's Route", $gpxFile->routes[0]->name);
	}
}