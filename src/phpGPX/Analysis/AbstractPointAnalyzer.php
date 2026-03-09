<?php

namespace phpGPX\Analysis;

use phpGPX\Models\GpxFile;
use phpGPX\Models\Track;

/**
 * Base class for point analyzers with no-op defaults.
 *
 * Most analyzers only need to implement {@see begin()}, {@see visit()}, and
 * {@see end()}. This base class provides empty implementations of
 * {@see aggregateTrack()} and {@see finalizeFile()} so you only override
 * what you actually need.
 *
 * ## Creating a custom analyzer
 *
 * ```php
 * use phpGPX\Analysis\AbstractPointAnalyzer;
 * use phpGPX\Models\Point;
 * use phpGPX\Models\Stats;
 *
 * class MaxSpeedAnalyzer extends AbstractPointAnalyzer
 * {
 *     private float $maxSpeed = 0;
 *
 *     public function begin(): void
 *     {
 *         $this->maxSpeed = 0;
 *     }
 *
 *     public function visit(Point $current, ?Point $previous): void
 *     {
 *         if ($previous === null) return;
 *         // ... compute speed, track max ...
 *     }
 *
 *     public function end(Stats $stats): void
 *     {
 *         // Write to a custom Stats field or extension
 *     }
 * }
 * ```
 *
 * @see PointAnalyzerInterface for the full lifecycle contract
 */
abstract class AbstractPointAnalyzer implements PointAnalyzerInterface
{
	public function aggregateTrack(Track $track): void
	{
		// No-op by default — override if your analyzer needs track-level aggregation.
	}

	public function finalizeFile(GpxFile $gpxFile): void
	{
		// No-op by default — override for file-level post-processing.
	}
}
