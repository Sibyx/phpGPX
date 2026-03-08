<?php

namespace phpGPX\Tests\Unit\Helpers;

use phpGPX\Helpers\ElevationGainLossCalculator;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;
use PHPUnit\Framework\TestCase;

class ElevationGainLossCalculatorTest extends TestCase
{
	private function makePoint(float $ele): Point
	{
		$p = new Point(PointType::Trackpoint);
		$p->latitude = 46.57;
		$p->longitude = 8.41;
		$p->elevation = $ele;
		return $p;
	}

	public function testEmptyPoints(): void
	{
		[$gain, $loss] = ElevationGainLossCalculator::calculate([]);
		$this->assertEqualsWithDelta(0.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testSinglePoint(): void
	{
		[$gain, $loss] = ElevationGainLossCalculator::calculate([$this->makePoint(100)]);
		$this->assertEqualsWithDelta(0.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testFlatTrack(): void
	{
		$points = [
			$this->makePoint(100),
			$this->makePoint(100),
			$this->makePoint(100),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate($points);
		$this->assertEqualsWithDelta(0.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testUphillOnly(): void
	{
		$points = [
			$this->makePoint(100),
			$this->makePoint(150),
			$this->makePoint(200),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate($points);
		$this->assertEqualsWithDelta(100.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testDownhillOnly(): void
	{
		$points = [
			$this->makePoint(200),
			$this->makePoint(150),
			$this->makePoint(100),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate($points);
		$this->assertEqualsWithDelta(0.0, $gain, 0.001);
		$this->assertEqualsWithDelta(100.0, $loss, 0.001);
	}

	public function testUpAndDown(): void
	{
		// Up 50, down 30, up 20
		$points = [
			$this->makePoint(100),
			$this->makePoint(150),
			$this->makePoint(120),
			$this->makePoint(140),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate($points);
		$this->assertEqualsWithDelta(70.0, $gain, 0.001);  // 50 + 20
		$this->assertEqualsWithDelta(30.0, $loss, 0.001);
	}

	public function testNullElevationSkipped(): void
	{
		$p1 = $this->makePoint(100);
		$p2 = new Point(PointType::Trackpoint);
		$p2->latitude = 46.57;
		$p2->longitude = 8.41;
		$p2->elevation = null;
		$p3 = $this->makePoint(200);

		[$gain, $loss] = ElevationGainLossCalculator::calculate([$p1, $p2, $p3]);
		$this->assertEqualsWithDelta(100.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testIgnoreElevationZero(): void
	{
		$points = [
			$this->makePoint(100),
			$this->makePoint(0),   // should be skipped
			$this->makePoint(200),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate($points, ignoreZeroElevation: true);
		$this->assertEqualsWithDelta(100.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testIgnoreElevationZeroDisabled(): void
	{
		$points = [
			$this->makePoint(100),
			$this->makePoint(0),
			$this->makePoint(200),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate($points, ignoreZeroElevation: false);
		$this->assertEqualsWithDelta(200.0, $gain, 0.001);   // 0→200
		$this->assertEqualsWithDelta(100.0, $loss, 0.001);   // 100→0
	}

	public function testSmoothingFiltersSmallChanges(): void
	{
		// Small oscillations of 2m — below 5m threshold, should be filtered
		$points = [
			$this->makePoint(100),
			$this->makePoint(102),
			$this->makePoint(100),
			$this->makePoint(102),
			$this->makePoint(100),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate(
			$points,
			applySmoothing: true,
			smoothingThreshold: 5,
		);
		$this->assertEqualsWithDelta(0.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testSmoothingKeepsLargeChanges(): void
	{
		// Large change of 50m — above 5m threshold
		$points = [
			$this->makePoint(100),
			$this->makePoint(150),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate(
			$points,
			applySmoothing: true,
			smoothingThreshold: 5,
		);
		$this->assertEqualsWithDelta(50.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}

	public function testSmoothingSpikesThreshold(): void
	{
		// Spike of 100m — above spikes threshold, should be filtered
		$points = [
			$this->makePoint(100),
			$this->makePoint(200),  // +100m spike, above 50m threshold
			$this->makePoint(105),
		];

		[$gain, $loss] = ElevationGainLossCalculator::calculate(
			$points,
			applySmoothing: true,
			smoothingThreshold: 2,
			spikesThreshold: 50,
		);
		// The 100m jump is filtered (> spikes threshold)
		// The 200→105 drop: delta from last considered (100) to 200 is 100 (filtered)
		// delta from 100 to 105 is 5 (above 2, below 50) — counted
		$this->assertEqualsWithDelta(5.0, $gain, 0.001);
		$this->assertEqualsWithDelta(0.0, $loss, 0.001);
	}
}