<?php

namespace phpGPX\Analysis;

use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Computes cumulative distance (raw and real) from GPS coordinates.
 *
 * **Raw distance** is the 2D ground distance computed via the Haversine formula.
 * **Real distance** includes elevation differences (3D Pythagorean distance).
 *
 * ## Smoothing
 *
 * When GPS jitter produces tiny movements that inflate total distance, enable
 * smoothing to ignore point-to-point distances below a threshold:
 *
 * ```php
 * new DistanceAnalyzer(applySmoothing: true, smoothingThreshold: 2)
 * ```
 *
 * Points below the threshold are skipped — the distance to the next significant
 * point is measured from the last accepted point, not the skipped one.
 *
 * ## Side effects
 *
 * For each point (except the first), this analyzer sets:
 * - `$point->difference` — raw distance from the previous accepted point
 * - `$point->distance`   — cumulative raw distance from the start
 *
 * ## Track aggregation
 *
 * Track distance = sum of segment distances.
 */
class DistanceAnalyzer extends AbstractPointAnalyzer
{
	private float $rawDistance;
	private float $realDistance;
	private ?Point $lastAcceptedPoint;

	public function __construct(
		private bool $applySmoothing = false,
		private int $smoothingThreshold = 2,
	) {
	}

	public function begin(): void
	{
		$this->rawDistance = 0;
		$this->realDistance = 0;
		$this->lastAcceptedPoint = null;
	}

	public function visit(Point $current, ?Point $previous): void
	{
		if ($this->lastAcceptedPoint === null) {
			$this->lastAcceptedPoint = $current;
			return;
		}

		$rawDiff = GeoHelper::getRawDistance($this->lastAcceptedPoint, $current);
		$realDiff = GeoHelper::getRealDistance($this->lastAcceptedPoint, $current);

		$current->difference = $rawDiff;

		if ($this->applySmoothing) {
			if ($rawDiff > $this->smoothingThreshold) {
				$this->rawDistance += $rawDiff;
				$this->realDistance += $realDiff;
				$this->lastAcceptedPoint = $current;
			}
		} else {
			$this->rawDistance += $rawDiff;
			$this->realDistance += $realDiff;
			$this->lastAcceptedPoint = $current;
		}

		$current->distance = $this->rawDistance;
	}

	public function end(Stats $stats): void
	{
		$stats->distance = $this->rawDistance;
		$stats->realDistance = $this->realDistance;
	}

	public function aggregateTrack(Track $track): void
	{
		if ($track->stats === null) {
			return;
		}

		$totalRaw = 0.0;
		$totalReal = 0.0;

		foreach ($track->segments as $segment) {
			$totalRaw += $segment->stats->distance ?? 0;
			$totalReal += $segment->stats->realDistance ?? 0;
		}

		$track->stats->distance = $totalRaw;
		$track->stats->realDistance = $totalReal;
	}
}
