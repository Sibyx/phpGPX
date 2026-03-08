<?php

namespace phpGPX;

/**
 * Class Config
 * Configuration value object for a phpGPX instance.
 * @package phpGPX
 */
class Config
{
	public function __construct(
		/** Calculate stats for tracks, segments and routes */
		public bool $calculateStats = true,

		/** Sort points by timestamp in Routes & Tracks on XML read */
		public bool $sortByTimestamp = false,

		/** Pretty print XML and JSON output */
		public bool $prettyPrint = true,

		/** Ignore points with elevation of 0 in stats calculation */
		public bool $ignoreZeroElevation = false,

		/** Apply elevation gain/loss smoothing */
		public bool $applyElevationSmoothing = false,

		/** Minimum elevation difference in meters for smoothing */
		public int $elevationSmoothingThreshold = 2,

		/** Maximum elevation difference in meters for spike filtering */
		public ?int $elevationSmoothingSpikesThreshold = null,

		/** Apply distance calculation smoothing */
		public bool $applyDistanceSmoothing = false,

		/** Minimum distance in meters between considered points for smoothing */
		public int $distanceSmoothingThreshold = 2,
	) {}
}