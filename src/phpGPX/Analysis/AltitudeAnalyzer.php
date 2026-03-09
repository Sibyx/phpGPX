<?php

namespace phpGPX\Analysis;

use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Tracks minimum and maximum altitude with coordinates.
 *
 * Scans all points and records the elevation extremes. Unlike the old
 * implementation, this does **not** assume the first point has the min
 * altitude — it scans all points (#70).
 *
 * ## Configuration
 *
 * - **ignoreZeroElevation** — Skip points with elevation = 0, treating them
 *   as missing data rather than sea level.
 *
 * ## Track aggregation
 *
 * Track min = lowest segment min. Track max = highest segment max.
 * Coordinates are preserved from the segment that holds the extreme.
 */
class AltitudeAnalyzer extends AbstractPointAnalyzer
{
	private ?float $minAltitude;
	private ?array $minCoords;
	private ?float $maxAltitude;
	private ?array $maxCoords;

	public function __construct(
		private bool $ignoreZeroElevation = false,
	) {
	}

	public function begin(): void
	{
		$this->minAltitude = null;
		$this->minCoords = null;
		$this->maxAltitude = null;
		$this->maxCoords = null;
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

		$coords = ['lat' => $current->latitude, 'lng' => $current->longitude];

		if ($this->maxAltitude === null || $elevation > $this->maxAltitude) {
			$this->maxAltitude = $elevation;
			$this->maxCoords = $coords;
		}

		if ($this->minAltitude === null || $elevation < $this->minAltitude) {
			$this->minAltitude = $elevation;
			$this->minCoords = $coords;
		}
	}

	public function end(Stats $stats): void
	{
		$stats->maxAltitude = $this->maxAltitude;
		$stats->maxAltitudeCoords = $this->maxCoords;
		$stats->minAltitude = $this->minAltitude;
		$stats->minAltitudeCoords = $this->minCoords;
	}

	public function aggregateTrack(Track $track): void
	{
		if ($track->stats === null) {
			return;
		}

		foreach ($track->segments as $segment) {
			if ($segment->stats === null) {
				continue;
			}

			if ($segment->stats->maxAltitude !== null
				&& ($track->stats->maxAltitude === null || $segment->stats->maxAltitude > $track->stats->maxAltitude)) {
				$track->stats->maxAltitude = $segment->stats->maxAltitude;
				$track->stats->maxAltitudeCoords = $segment->stats->maxAltitudeCoords;
			}

			if ($segment->stats->minAltitude !== null
				&& ($track->stats->minAltitude === null || $segment->stats->minAltitude < $track->stats->minAltitude)) {
				$track->stats->minAltitude = $segment->stats->minAltitude;
				$track->stats->minAltitudeCoords = $segment->stats->minAltitudeCoords;
			}
		}
	}
}
