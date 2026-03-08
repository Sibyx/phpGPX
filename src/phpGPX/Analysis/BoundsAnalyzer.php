<?php

namespace phpGPX\Analysis;

use phpGPX\Models\Bounds;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Computes geographic bounds (min/max latitude and longitude).
 *
 * Tracks the bounding box of all points at three levels:
 *
 * 1. **Segment / Route** — bounds of the points within
 * 2. **Track** — merged bounds across all segments
 * 3. **File** — merged bounds across tracks, routes, and waypoints
 *    (set on `GpxFile::$metadata->bounds` via {@see finalizeFile()})
 *
 * Points with null coordinates are silently skipped.
 *
 * ## File-level bounds (#28)
 *
 * This is the only built-in analyzer that uses {@see finalizeFile()} — it
 * needs to include waypoints in the file-level bounding box, and waypoints
 * are not part of any track or route.
 */
class BoundsAnalyzer extends AbstractPointAnalyzer
{
	private float $minLat;
	private float $maxLat;
	private float $minLon;
	private float $maxLon;
	private bool $hasData;

	/** Accumulated file-level bounds across all tracks, routes, and waypoints. */
	private float $fileMinLat;
	private float $fileMaxLat;
	private float $fileMinLon;
	private float $fileMaxLon;
	private bool $fileHasData = false;

	public function begin(): void
	{
		$this->minLat = PHP_FLOAT_MAX;
		$this->maxLat = -PHP_FLOAT_MAX;
		$this->minLon = PHP_FLOAT_MAX;
		$this->maxLon = -PHP_FLOAT_MAX;
		$this->hasData = false;
	}

	public function visit(Point $current, ?Point $previous): void
	{
		if ($current->latitude === null || $current->longitude === null) {
			return;
		}

		$this->hasData = true;

		if ($current->latitude < $this->minLat) {
			$this->minLat = $current->latitude;
		}
		if ($current->latitude > $this->maxLat) {
			$this->maxLat = $current->latitude;
		}
		if ($current->longitude < $this->minLon) {
			$this->minLon = $current->longitude;
		}
		if ($current->longitude > $this->maxLon) {
			$this->maxLon = $current->longitude;
		}

		// Accumulate for file-level bounds
		$this->expandFileBounds($current->latitude, $current->longitude);
	}

	public function end(Stats $stats): void
	{
		$stats->bounds = $this->hasData
			? new Bounds($this->minLat, $this->minLon, $this->maxLat, $this->maxLon)
			: null;
	}

	public function aggregateTrack(Track $track): void
	{
		if ($track->stats === null) {
			return;
		}

		// Merge segment bounds into track bounds
		$minLat = PHP_FLOAT_MAX;
		$maxLat = -PHP_FLOAT_MAX;
		$minLon = PHP_FLOAT_MAX;
		$maxLon = -PHP_FLOAT_MAX;
		$hasData = false;

		foreach ($track->segments as $segment) {
			if ($segment->stats?->bounds === null) {
				continue;
			}

			$hasData = true;
			$b = $segment->stats->bounds;

			if ($b->minLatitude < $minLat) {
				$minLat = $b->minLatitude;
			}
			if ($b->maxLatitude > $maxLat) {
				$maxLat = $b->maxLatitude;
			}
			if ($b->minLongitude < $minLon) {
				$minLon = $b->minLongitude;
			}
			if ($b->maxLongitude > $maxLon) {
				$maxLon = $b->maxLongitude;
			}
		}

		$track->stats->bounds = $hasData
			? new Bounds($minLat, $minLon, $maxLat, $maxLon)
			: null;
	}

	public function finalizeFile(GpxFile $gpxFile): void
	{
		// Include waypoints in file-level bounds
		foreach ($gpxFile->waypoints as $waypoint) {
			if ($waypoint->latitude !== null && $waypoint->longitude !== null) {
				$this->expandFileBounds($waypoint->latitude, $waypoint->longitude);
			}
		}

		if (!$this->fileHasData) {
			return;
		}

		if ($gpxFile->metadata === null) {
			$gpxFile->metadata = new Metadata();
		}

		$gpxFile->metadata->bounds = new Bounds(
			$this->fileMinLat,
			$this->fileMinLon,
			$this->fileMaxLat,
			$this->fileMaxLon,
		);
	}

	/**
	 * Expand the file-level bounding box with a coordinate.
	 */
	private function expandFileBounds(float $lat, float $lon): void
	{
		if (!$this->fileHasData) {
			$this->fileMinLat = $lat;
			$this->fileMaxLat = $lat;
			$this->fileMinLon = $lon;
			$this->fileMaxLon = $lon;
			$this->fileHasData = true;
			return;
		}

		if ($lat < $this->fileMinLat) {
			$this->fileMinLat = $lat;
		}
		if ($lat > $this->fileMaxLat) {
			$this->fileMaxLat = $lat;
		}
		if ($lon < $this->fileMinLon) {
			$this->fileMinLon = $lon;
		}
		if ($lon > $this->fileMaxLon) {
			$this->fileMaxLon = $lon;
		}
	}
}