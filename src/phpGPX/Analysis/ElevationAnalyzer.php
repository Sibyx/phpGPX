<?php

namespace phpGPX\Analysis;

use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Computes cumulative elevation gain and loss.
 *
 * Iterates consecutive point pairs and sums positive elevation deltas (gain)
 * and negative elevation deltas (loss). Skips points with null elevation.
 *
 * ## Configuration
 *
 * - **ignoreZeroElevation** — Treat elevation = 0 as missing data (common in
 *   GPS tracks where the device reports 0 when it has no fix).
 *
 * - **applySmoothing** — Only count elevation changes that exceed a threshold,
 *   filtering out noise from barometric altitude sensors:
 *   ```php
 *   new ElevationAnalyzer(applySmoothing: true, smoothingThreshold: 3)
 *   ```
 *
 * - **spikesThreshold** — When smoothing, also ignore changes larger than
 *   this value (outlier spike protection):
 *   ```php
 *   new ElevationAnalyzer(
 *       applySmoothing: true,
 *       smoothingThreshold: 2,
 *       spikesThreshold: 100,
 *   )
 *   ```
 *
 * ## Track aggregation
 *
 * Track gain = sum of segment gains. Track loss = sum of segment losses.
 */
class ElevationAnalyzer extends AbstractPointAnalyzer
{
	private float $gain;
	private float $loss;
	private ?float $lastElevation;

	public function __construct(
		private bool $ignoreZeroElevation = false,
		private bool $applySmoothing = false,
		private int $smoothingThreshold = 2,
		private ?int $spikesThreshold = null,
	) {
	}

	public function begin(): void
	{
		$this->gain = 0;
		$this->loss = 0;
		$this->lastElevation = null;
	}

	public function visit(Point $current, ?Point $previous): void
	{
		$elevation = $current->elevation;

		if ($elevation === null) {
			return;
		}

		if ($this->ignoreZeroElevation && $elevation == 0) {
			return;
		}

		if ($this->lastElevation === null) {
			$this->lastElevation = $elevation;
			return;
		}

		$delta = $elevation - $this->lastElevation;

		if ($this->applySmoothing) {
			$absDelta = abs($delta);

			if ($absDelta > $this->smoothingThreshold
				&& ($this->spikesThreshold === null || $absDelta < $this->spikesThreshold)) {
				$this->gain += ($delta > 0) ? $delta : 0;
				$this->loss += ($delta < 0) ? abs($delta) : 0;
				$this->lastElevation = $elevation;
			}
		} else {
			$this->gain += ($delta > 0) ? $delta : 0;
			$this->loss += ($delta < 0) ? abs($delta) : 0;
			$this->lastElevation = $elevation;
		}
	}

	public function end(Stats $stats): void
	{
		$stats->cumulativeElevationGain = $this->gain;
		$stats->cumulativeElevationLoss = $this->loss;
	}

	public function aggregateTrack(Track $track): void
	{
		if ($track->stats === null) {
			return;
		}

		$totalGain = 0.0;
		$totalLoss = 0.0;

		foreach ($track->segments as $segment) {
			$totalGain += $segment->stats->cumulativeElevationGain ?? 0;
			$totalLoss += $segment->stats->cumulativeElevationLoss ?? 0;
		}

		$track->stats->cumulativeElevationGain = $totalGain;
		$track->stats->cumulativeElevationLoss = $totalLoss;
	}
}
