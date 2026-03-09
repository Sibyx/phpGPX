<?php

namespace phpGPX\Analysis;

use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Computes moving duration by detecting movement between consecutive points.
 *
 * For each pair of consecutive points with timestamps, computes instantaneous
 * speed = distance / time. Points above the speed threshold count as "moving".
 *
 * ## Configuration
 *
 * - **speedThreshold** — Minimum speed in m/s to count as moving (default 0.5).
 *   A threshold of 0.5 m/s ≈ 1.8 km/h filters out GPS drift while standing still.
 *   ```php
 *   new MovementAnalyzer(speedThreshold: 1.0)  // 3.6 km/h — walking pace
 *   ```
 *
 * ## Results
 *
 * - `Stats::$movingDuration` — Total seconds spent moving
 * - `Stats::$movingAverageSpeed` — Computed by the engine: distance / movingDuration
 *
 * ## Track aggregation
 *
 * Track moving duration = sum of segment moving durations.
 */
class MovementAnalyzer extends AbstractPointAnalyzer
{
	private float $movingSeconds;
	private bool $hasTimestamps;

	public function __construct(
		private float $speedThreshold = 0.5,
	) {
	}

	public function begin(): void
	{
		$this->movingSeconds = 0;
		$this->hasTimestamps = false;
	}

	public function visit(Point $current, ?Point $previous): void
	{
		if ($previous === null) {
			return;
		}

		if ($previous->time === null || $current->time === null) {
			return;
		}

		$this->hasTimestamps = true;

		$timeDelta = abs($current->time->getTimestamp() - $previous->time->getTimestamp());

		if ($timeDelta === 0) {
			return;
		}

		$distance = GeoHelper::getRawDistance($previous, $current);
		$speed = $distance / $timeDelta;

		if ($speed >= $this->speedThreshold) {
			$this->movingSeconds += $timeDelta;
		}
	}

	public function end(Stats $stats): void
	{
		$stats->movingDuration = $this->hasTimestamps ? $this->movingSeconds : null;
		// movingAverageSpeed is computed by the engine (depends on distance)
	}

	public function aggregateTrack(Track $track): void
	{
		if ($track->stats === null) {
			return;
		}

		$totalMoving = 0.0;
		$hasData = false;

		foreach ($track->segments as $segment) {
			if ($segment->stats?->movingDuration !== null) {
				$totalMoving += $segment->stats->movingDuration;
				$hasData = true;
			}
		}

		$track->stats->movingDuration = $hasData ? $totalMoving : null;
		// movingAverageSpeed is computed by the engine
	}
}
