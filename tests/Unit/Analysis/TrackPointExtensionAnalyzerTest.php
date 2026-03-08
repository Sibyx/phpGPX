<?php

namespace phpGPX\Tests\Unit\Analysis;

use phpGPX\Analysis\Engine;
use phpGPX\Analysis\TrackPointExtensionAnalyzer;
use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;
use phpGPX\Models\Route;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use PHPUnit\Framework\TestCase;

class TrackPointExtensionAnalyzerTest extends TestCase
{
	private Engine $engine;

	protected function setUp(): void
	{
		$this->engine = (new Engine())->addAnalyzer(new TrackPointExtensionAnalyzer());
	}

	private function makePointWithExtension(
		float $lat,
		float $lon,
		?float $hr = null,
		?float $cad = null,
		?float $aTemp = null
	): Point {
		$p = new Point(PointType::Trackpoint);
		$p->latitude = $lat;
		$p->longitude = $lon;

		if ($hr !== null || $cad !== null || $aTemp !== null) {
			$ext = new TrackPointExtension();
			$ext->hr = $hr;
			$ext->cad = $cad;
			$ext->aTemp = $aTemp;

			$extensions = new Extensions();
			$extensions->set($ext);
			$p->extensions = $extensions;
		}

		return $p;
	}

	public function testSegmentHeartRateStats(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePointWithExtension(48.0, 17.0, hr: 120.0),
			$this->makePointWithExtension(48.1, 17.1, hr: 140.0),
			$this->makePointWithExtension(48.2, 17.2, hr: 160.0),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$segStats = $result->tracks[0]->segments[0]->stats;
		$this->assertEqualsWithDelta(140.0, $segStats->averageHeartRate, 0.001);
		$this->assertEqualsWithDelta(160.0, $segStats->maxHeartRate, 0.001);
	}

	public function testSegmentCadenceStats(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePointWithExtension(48.0, 17.0, cad: 80.0),
			$this->makePointWithExtension(48.1, 17.1, cad: 90.0),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$this->assertEqualsWithDelta(85.0, $result->tracks[0]->segments[0]->stats->averageCadence, 0.001);
	}

	public function testSegmentTemperatureStats(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePointWithExtension(48.0, 17.0, aTemp: 20.0),
			$this->makePointWithExtension(48.1, 17.1, aTemp: 24.0),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$this->assertEqualsWithDelta(22.0, $result->tracks[0]->segments[0]->stats->averageTemperature, 0.001);
	}

	public function testTrackAggregatesAcrossSegments(): void
	{
		$seg1 = new Segment();
		$seg1->points = [
			$this->makePointWithExtension(48.0, 17.0, hr: 100.0),
			$this->makePointWithExtension(48.1, 17.1, hr: 120.0),
		];

		$seg2 = new Segment();
		$seg2->points = [
			$this->makePointWithExtension(48.2, 17.2, hr: 140.0),
			$this->makePointWithExtension(48.3, 17.3, hr: 160.0),
		];

		$track = new Track();
		$track->segments = [$seg1, $seg2];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$trackStats = $result->tracks[0]->stats;
		// Weighted average: (100+120+140+160)/4 = 130
		$this->assertEqualsWithDelta(130.0, $trackStats->averageHeartRate, 0.001);
		$this->assertEqualsWithDelta(160.0, $trackStats->maxHeartRate, 0.001);
	}

	public function testNoExtensionDataLeavesStatsNull(): void
	{
		$segment = new Segment();
		$segment->points = [
			$this->makePointWithExtension(48.0, 17.0),
			$this->makePointWithExtension(48.1, 17.1),
		];

		$track = new Track();
		$track->segments = [$segment];

		$gpx = new GpxFile();
		$gpx->tracks = [$track];

		$result = $this->engine->process($gpx);

		$this->assertNull($result->tracks[0]->segments[0]->stats->averageHeartRate);
		$this->assertNull($result->tracks[0]->segments[0]->stats->averageCadence);
		$this->assertNull($result->tracks[0]->segments[0]->stats->averageTemperature);
	}

	public function testRouteExtensionStats(): void
	{
		$route = new Route();
		$route->points = [
			$this->makePointWithExtension(48.0, 17.0, hr: 130.0),
			$this->makePointWithExtension(48.1, 17.1, hr: 150.0),
		];

		$gpx = new GpxFile();
		$gpx->routes = [$route];

		$result = $this->engine->process($gpx);

		$this->assertEqualsWithDelta(140.0, $result->routes[0]->stats->averageHeartRate, 0.001);
	}
}