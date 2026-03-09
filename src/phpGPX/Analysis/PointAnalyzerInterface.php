<?php

namespace phpGPX\Analysis;

use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Contract for single-pass point analyzers.
 *
 * A PointAnalyzer participates in the {@see Engine} lifecycle. The engine
 * walks the GPX structure once, calling registered analyzers at each stage:
 *
 *     for each track:
 *         for each segment:
 *             begin()                          ← reset per-segment state
 *             visit(point, prev) × N           ← accumulate from each point
 *             end(segmentStats)                ← write results to segment stats
 *         aggregateTrack(track)                ← combine segments → track stats
 *     for each route:
 *         begin() → visit() × N → end()       ← same as segment
 *     finalizeFile(gpxFile)                    ← optional file-level work
 *
 * Each analyzer is a small, focused class responsible for one aspect of
 * statistics (distance, elevation, bounds, etc.). The engine ensures all
 * analyzers see every point in a single pass — no redundant iteration.
 *
 * @see AbstractPointAnalyzer for a convenient base class with no-op defaults
 * @see Engine for the orchestrating engine
 */
interface PointAnalyzerInterface
{
	/**
	 * Reset internal state before processing a new set of points.
	 *
	 * Called once per segment (or once per route). Use this to zero out
	 * accumulators, reset min/max trackers, etc.
	 */
	public function begin(): void;

	/**
	 * Process a single point in sequence.
	 *
	 * Called for every point in the current segment/route. The engine provides
	 * both the current point and the previous point (null for the first point).
	 *
	 * @param Point      $current  The point being visited
	 * @param Point|null $previous The preceding point, or null if this is the first
	 */
	public function visit(Point $current, ?Point $previous): void;

	/**
	 * Write accumulated results into the Stats object.
	 *
	 * Called after the last point in a segment/route has been visited.
	 * The Stats object is shared across all analyzers — write only the
	 * fields your analyzer is responsible for.
	 *
	 * @param Stats $stats The segment-level or route-level Stats to populate
	 */
	public function end(Stats $stats): void;

	/**
	 * Aggregate segment-level stats into track-level stats.
	 *
	 * Called once per track, after all segments have been processed. Use this
	 * to sum distances, merge bounds, find extremes across segments, etc.
	 *
	 * The track's Stats object has already been created by the engine.
	 * Segment stats are available via `$track->segments[*]->stats`.
	 *
	 * @param Track $track The track with fully populated segment stats
	 */
	public function aggregateTrack(Track $track): void;

	/**
	 * Optional file-level post-processing.
	 *
	 * Called once after all tracks and routes have been processed. Use this
	 * for cross-cutting concerns like computing file-level bounds that include
	 * waypoints, or setting metadata properties.
	 *
	 * Most analyzers can leave this as a no-op.
	 *
	 * @param GpxFile $gpxFile The fully processed GPX file
	 */
	public function finalizeFile(GpxFile $gpxFile): void;
}
