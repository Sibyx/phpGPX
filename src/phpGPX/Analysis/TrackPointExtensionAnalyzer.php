<?php

namespace phpGPX\Analysis;

use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Aggregates sensor data from Garmin TrackPointExtension.
 *
 * Collects heart rate, cadence, and temperature values from points that carry
 * a {@see \phpGPX\Models\Extensions\TrackPointExtension} and computes:
 *
 * - `Stats::$averageHeartRate` — Mean HR across all points with data
 * - `Stats::$maxHeartRate`     — Peak HR
 * - `Stats::$averageCadence`   — Mean cadence (RPM)
 * - `Stats::$averageTemperature` — Mean ambient temperature (°C)
 *
 * Points without extension data are silently skipped. If no points in a
 * segment have extension data, the corresponding Stats fields remain null.
 *
 * ## Track aggregation
 *
 * Track-level averages are the **weighted average** across all points in all
 * segments (not the average of segment averages). This ensures correct results
 * when segments have different numbers of points.
 *
 * Internally, the analyzer maintains running sums and counts across segments
 * for the current track, then resets after aggregation.
 */
class TrackPointExtensionAnalyzer extends AbstractPointAnalyzer
{
	// Per-segment accumulators
	private float $hrSum;
	private int $hrCount;
	private ?float $hrMax;
	private float $cadSum;
	private int $cadCount;
	private float $tempSum;
	private int $tempCount;

	// Per-track accumulators (persist across segments, reset in aggregateTrack)
	private float $trackHrSum = 0;
	private int $trackHrCount = 0;
	private ?float $trackHrMax = null;
	private float $trackCadSum = 0;
	private int $trackCadCount = 0;
	private float $trackTempSum = 0;
	private int $trackTempCount = 0;

	public function begin(): void
	{
		$this->hrSum = 0;
		$this->hrCount = 0;
		$this->hrMax = null;
		$this->cadSum = 0;
		$this->cadCount = 0;
		$this->tempSum = 0;
		$this->tempCount = 0;
	}

	public function visit(Point $current, ?Point $previous): void
	{
		$ext = $current->extensions?->get(TrackPointExtension::class);

		if ($ext === null) {
			return;
		}

		$hr = $ext->hr ?? null;
		$cad = $ext->cad ?? null;
		$aTemp = $ext->aTemp ?? null;

		if ($hr !== null) {
			$this->hrSum += $hr;
			$this->hrCount++;
			$this->trackHrSum += $hr;
			$this->trackHrCount++;

			if ($this->hrMax === null || $hr > $this->hrMax) {
				$this->hrMax = $hr;
			}
			if ($this->trackHrMax === null || $hr > $this->trackHrMax) {
				$this->trackHrMax = $hr;
			}
		}

		if ($cad !== null) {
			$this->cadSum += $cad;
			$this->cadCount++;
			$this->trackCadSum += $cad;
			$this->trackCadCount++;
		}

		if ($aTemp !== null) {
			$this->tempSum += $aTemp;
			$this->tempCount++;
			$this->trackTempSum += $aTemp;
			$this->trackTempCount++;
		}
	}

	public function end(Stats $stats): void
	{
		if ($this->hrCount > 0) {
			$stats->averageHeartRate = $this->hrSum / $this->hrCount;
			$stats->maxHeartRate = $this->hrMax;
		}

		if ($this->cadCount > 0) {
			$stats->averageCadence = $this->cadSum / $this->cadCount;
		}

		if ($this->tempCount > 0) {
			$stats->averageTemperature = $this->tempSum / $this->tempCount;
		}
	}

	public function aggregateTrack(Track $track): void
	{
		if ($track->stats === null) {
			$this->resetTrackAccumulators();
			return;
		}

		if ($this->trackHrCount > 0) {
			$track->stats->averageHeartRate = $this->trackHrSum / $this->trackHrCount;
			$track->stats->maxHeartRate = $this->trackHrMax;
		}

		if ($this->trackCadCount > 0) {
			$track->stats->averageCadence = $this->trackCadSum / $this->trackCadCount;
		}

		if ($this->trackTempCount > 0) {
			$track->stats->averageTemperature = $this->trackTempSum / $this->trackTempCount;
		}

		$this->resetTrackAccumulators();
	}

	private function resetTrackAccumulators(): void
	{
		$this->trackHrSum = 0;
		$this->trackHrCount = 0;
		$this->trackHrMax = null;
		$this->trackCadSum = 0;
		$this->trackCadCount = 0;
		$this->trackTempSum = 0;
		$this->trackTempCount = 0;
	}
}
