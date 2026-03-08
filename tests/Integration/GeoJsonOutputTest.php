<?php

namespace phpGPX\Tests\Integration;

use phpGPX\Models\Point;
use phpGPX\Models\Route;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use phpGPX\phpGPX;
use PHPUnit\Framework\TestCase;

class GeoJsonOutputTest extends TestCase
{
	private const FIXTURES_DIR = __DIR__ . '/../Fixtures';

	private phpGPX $gpx;

	protected function setUp(): void
	{
		$this->gpx = new phpGPX();
	}

	public function testGpxFileJsonSerializeIsFeatureCollection(): void
	{
		$gpxFile = $this->gpx->load(self::FIXTURES_DIR . '/route.gpx');
		$json = $gpxFile->jsonSerialize();

		$this->assertEquals('FeatureCollection', $json['type']);
		$this->assertArrayHasKey('features', $json);
		$this->assertIsArray($json['features']);
	}

	public function testWaypointJsonIsPointFeature(): void
	{
		$point = new Point(Point::WAYPOINT);
		$point->latitude = 49.363;
		$point->longitude = 0.080;
		$point->elevation = 100.0;
		$point->name = 'Test Waypoint';

		$json = $point->jsonSerialize();

		$this->assertEquals('Feature', $json['type']);
		$this->assertEquals('Point', $json['geometry']['type']);
		$this->assertCount(3, $json['geometry']['coordinates']);
		$this->assertEqualsWithDelta(0.080, $json['geometry']['coordinates'][0], 0.001);
		$this->assertEqualsWithDelta(49.363, $json['geometry']['coordinates'][1], 0.001);
		$this->assertEqualsWithDelta(100.0, $json['geometry']['coordinates'][2], 0.001);
		$this->assertEquals('Test Waypoint', $json['properties']['name']);
	}

	public function testRouteJsonIsLineStringFeature(): void
	{
		$route = new Route();
		$route->name = 'Test Route';

		$p1 = new Point(Point::ROUTEPOINT);
		$p1->latitude = 54.932;
		$p1->longitude = 9.860;
		$p1->elevation = 0.0;

		$p2 = new Point(Point::ROUTEPOINT);
		$p2->latitude = 54.933;
		$p2->longitude = 9.861;
		$p2->elevation = 1.0;

		$route->points = [$p1, $p2];

		$json = $route->jsonSerialize();

		$this->assertEquals('Feature', $json['type']);
		$this->assertEquals('LineString', $json['geometry']['type']);
		$this->assertCount(2, $json['geometry']['coordinates']);

		// GeoJSON uses [lon, lat, ele]
		$this->assertEqualsWithDelta(9.860, $json['geometry']['coordinates'][0][0], 0.001);
		$this->assertEqualsWithDelta(54.932, $json['geometry']['coordinates'][0][1], 0.001);
		$this->assertEquals('Test Route', $json['properties']['name']);
	}

	public function testTrackJsonIsMultiLineStringFeature(): void
	{
		$track = new Track();
		$track->name = 'Test Track';

		$seg1 = new Segment();
		$p1 = new Point(Point::TRACKPOINT);
		$p1->latitude = 46.571;
		$p1->longitude = 8.414;
		$p1->elevation = 2419.0;

		$p2 = new Point(Point::TRACKPOINT);
		$p2->latitude = 46.572;
		$p2->longitude = 8.415;
		$p2->elevation = 2420.0;
		$seg1->points = [$p1, $p2];

		$seg2 = new Segment();
		$p3 = new Point(Point::TRACKPOINT);
		$p3->latitude = 46.573;
		$p3->longitude = 8.416;
		$p3->elevation = 2421.0;
		$seg2->points = [$p3];

		$track->segments = [$seg1, $seg2];

		$json = $track->jsonSerialize();

		$this->assertEquals('Feature', $json['type']);
		$this->assertEquals('MultiLineString', $json['geometry']['type']);
		$this->assertCount(2, $json['geometry']['coordinates']);
		$this->assertCount(2, $json['geometry']['coordinates'][0]); // seg1 has 2 points
		$this->assertCount(1, $json['geometry']['coordinates'][1]); // seg2 has 1 point
		$this->assertEquals('Test Track', $json['properties']['name']);
	}

	public function testLoadedFileGeoJsonStructure(): void
	{
		$gpxFile = $this->gpx->load(self::FIXTURES_DIR . '/minimal.gpx');
		$json = json_decode(json_encode($gpxFile), true);

		$this->assertEquals('FeatureCollection', $json['type']);

		// Should have features: 1 route + 1 track = 2 features (no waypoints in this file)
		$this->assertCount(2, $json['features']);

		// Route feature
		$routeFeature = $json['features'][0];
		$this->assertEquals('Feature', $routeFeature['type']);
		$this->assertEquals('LineString', $routeFeature['geometry']['type']);

		// Track feature
		$trackFeature = $json['features'][1];
		$this->assertEquals('Feature', $trackFeature['type']);
		$this->assertEquals('MultiLineString', $trackFeature['geometry']['type']);
	}

	public function testGeoJsonWithWaypoints(): void
	{
		$gpxFile = $this->gpx->load(self::FIXTURES_DIR . '/timezero.gpx');
		$json = json_decode(json_encode($gpxFile), true);

		$this->assertEquals('FeatureCollection', $json['type']);

		// 2 waypoints + 2 tracks = 4 features
		$this->assertCount(4, $json['features']);

		// First two should be waypoint Point features
		$this->assertEquals('Point', $json['features'][0]['geometry']['type']);
		$this->assertEquals('Point', $json['features'][1]['geometry']['type']);

		// Last two should be track MultiLineString features
		$this->assertEquals('MultiLineString', $json['features'][2]['geometry']['type']);
		$this->assertEquals('MultiLineString', $json['features'][3]['geometry']['type']);
	}

	public function testToJsonOutput(): void
	{
		$gpxFile = $this->gpx->load(self::FIXTURES_DIR . '/route.gpx');

		$geoJson = $gpxFile->toJSON();
		$decoded = json_decode($geoJson, true);
		$this->assertNotNull($decoded);
		$this->assertEquals('FeatureCollection', $decoded['type']);
		$this->assertArrayHasKey('features', $decoded);
	}
}