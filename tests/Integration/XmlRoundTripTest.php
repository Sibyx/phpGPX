<?php

namespace phpGPX\Tests\Integration;

use phpGPX\Analysis\Engine;
use phpGPX\phpGPX;
use PHPUnit\Framework\TestCase;

class XmlRoundTripTest extends TestCase
{
	private const FIXTURES_DIR = __DIR__ . '/../Fixtures';

	private phpGPX $gpx;

	protected function setUp(): void
	{
		$this->gpx = new phpGPX(engine: Engine::default());
	}

	/**
	 * Load a GPX file, serialize to XML, parse again, and verify key data is preserved.
	 */
	public function testRoundTripTimezero(): void
	{
		$original = $this->gpx->load(self::FIXTURES_DIR . '/timezero.gpx');
		$xml = $original->toXML()->saveXML();
		$reloaded = $this->gpx->parse($xml);

		$this->assertCount(count($original->waypoints), $reloaded->waypoints);
		$this->assertCount(count($original->tracks), $reloaded->tracks);

		// Verify waypoint data survives round-trip
		for ($i = 0; $i < count($original->waypoints); $i++) {
			$this->assertEqualsWithDelta(
				$original->waypoints[$i]->latitude,
				$reloaded->waypoints[$i]->latitude,
				0.0001
			);
			$this->assertEquals($original->waypoints[$i]->name, $reloaded->waypoints[$i]->name);
		}

		// Verify track structure survives round-trip
		for ($t = 0; $t < count($original->tracks); $t++) {
			$this->assertEquals($original->tracks[$t]->name, $reloaded->tracks[$t]->name);
			$this->assertCount(
				count($original->tracks[$t]->segments),
				$reloaded->tracks[$t]->segments
			);

			for ($s = 0; $s < count($original->tracks[$t]->segments); $s++) {
				$this->assertCount(
					count($original->tracks[$t]->segments[$s]->points),
					$reloaded->tracks[$t]->segments[$s]->points
				);
			}
		}
	}

	public function testRoundTripRoute(): void
	{
		$original = $this->gpx->load(self::FIXTURES_DIR . '/route.gpx');
		$xml = $original->toXML()->saveXML();
		$reloaded = $this->gpx->parse($xml);

		$this->assertCount(count($original->routes), $reloaded->routes);

		for ($r = 0; $r < count($original->routes); $r++) {
			$this->assertEquals($original->routes[$r]->name, $reloaded->routes[$r]->name);
			$this->assertCount(
				count($original->routes[$r]->points),
				$reloaded->routes[$r]->points
			);

			for ($p = 0; $p < count($original->routes[$r]->points); $p++) {
				$origPoint = $original->routes[$r]->points[$p];
				$reloadedPoint = $reloaded->routes[$r]->points[$p];

				$this->assertEqualsWithDelta($origPoint->latitude, $reloadedPoint->latitude, 0.0001);
				$this->assertEqualsWithDelta($origPoint->longitude, $reloadedPoint->longitude, 0.0001);
				$this->assertEquals($origPoint->name, $reloadedPoint->name);
			}
		}
	}

	public function testRoundTripGpsTrack(): void
	{
		$original = $this->gpx->load(self::FIXTURES_DIR . '/gps-track.gpx');
		$xml = $original->toXML()->saveXML();
		$reloaded = $this->gpx->parse($xml);

		$this->assertCount(1, $reloaded->tracks);
		$this->assertEquals('GPS-Track', $reloaded->tracks[0]->name);

		$origSeg = $original->tracks[0]->segments[0];
		$reloadedSeg = $reloaded->tracks[0]->segments[0];

		$this->assertCount(count($origSeg->points), $reloadedSeg->points);

		// Verify elevation survives round-trip
		for ($i = 0; $i < count($origSeg->points); $i++) {
			$this->assertEqualsWithDelta(
				$origSeg->points[$i]->elevation,
				$reloadedSeg->points[$i]->elevation,
				0.01
			);
		}
	}

	public function testRoundTripMinimalWithExtensions(): void
	{
		$original = $this->gpx->load(self::FIXTURES_DIR . '/minimal.gpx');
		$xml = $original->toXML()->saveXML();
		$reloaded = $this->gpx->parse($xml);

		// Metadata survives
		$this->assertNotNull($reloaded->metadata);
		$this->assertEquals($original->metadata->name, $reloaded->metadata->name);

		// Route survives
		$this->assertCount(1, $reloaded->routes);

		// Track with extensions survives
		$this->assertCount(1, $reloaded->tracks);
		$origPoint = $original->tracks[0]->segments[0]->points[0];
		$reloadedPoint = $reloaded->tracks[0]->segments[0]->points[0];

		$this->assertNotNull($reloadedPoint->extensions);
		$this->assertNotNull($reloadedPoint->extensions->get(\phpGPX\Models\Extensions\TrackPointExtension::class));
		$this->assertEqualsWithDelta(
			$origPoint->extensions->get(\phpGPX\Models\Extensions\TrackPointExtension::class)->hr,
			$reloadedPoint->extensions->get(\phpGPX\Models\Extensions\TrackPointExtension::class)->hr,
			0.1
		);
	}

	public function testRoundTripStatsConsistency(): void
	{
		$original = $this->gpx->load(self::FIXTURES_DIR . '/gps-track.gpx');
		$xml = $original->toXML()->saveXML();
		$reloaded = $this->gpx->parse($xml);

		$origStats = $original->tracks[0]->stats;
		$reloadedStats = $reloaded->tracks[0]->stats;

		$this->assertEqualsWithDelta($origStats->distance, $reloadedStats->distance, 0.01);
		$this->assertEqualsWithDelta($origStats->duration, $reloadedStats->duration, 0.1);
		$this->assertEqualsWithDelta($origStats->minAltitude, $reloadedStats->minAltitude, 0.01);
		$this->assertEqualsWithDelta($origStats->maxAltitude, $reloadedStats->maxAltitude, 0.01);
		$this->assertEqualsWithDelta(
			$origStats->cumulativeElevationGain,
			$reloadedStats->cumulativeElevationGain,
			0.01
		);
	}
}