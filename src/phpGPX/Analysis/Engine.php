<?php

namespace phpGPX\Analysis;

use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\Stats;

/**
 * Single-pass stats computation engine.
 *
 * The Engine walks the GPX structure **once** and dispatches each point
 * to all registered {@see PointAnalyzerInterface} analyzers in a single pass.
 *
 * ## How it works
 *
 * ```
 *  ┌─────────────┐     ┌──────────────────────────────────────────────┐
 *  │  GpxFile     │     │  Engine                                 │
 *  │              │     │                                              │
 *  │  tracks[]    │────▶│  (optional) sort points by timestamp         │
 *  │    segments[]│     │  for each track:                             │
 *  │      points[]│     │    for each segment:                         │
 *  │              │     │      analyzer.begin()    ← reset state      │
 *  │  routes[]    │     │      for each point:                         │
 *  │    points[]  │     │        analyzer.visit()   ← single pass     │
 *  └─────────────┘     │      analyzer.end()       ← write stats     │
 *                       │    analyzer.aggregateTrack()                 │
 *                       │  for each route: (same as segment)           │
 *                       │  analyzer.finalizeFile()                     │
 *                       └──────────────────────────────────────────────┘
 * ```
 *
 * ## Usage
 *
 * Quick start with all defaults:
 *
 * ```php
 * $file = (new phpGPX())->load('track.gpx');
 * $file = Engine::default()->process($file);
 * ```
 *
 * Custom configuration:
 *
 * ```php
 * $engine = Engine::default(
 *     sortByTimestamp: true,
 *     applyElevationSmoothing: true,
 *     elevationSmoothingThreshold: 2,
 * );
 * $file = $engine->process($file);
 * ```
 *
 * Manual analyzer selection:
 *
 * ```php
 * $engine = (new Engine())
 *     ->addAnalyzer(new DistanceAnalyzer())
 *     ->addAnalyzer(new ElevationAnalyzer(applySmoothing: true))
 *     ->addAnalyzer(new BoundsAnalyzer());
 * ```
 *
 * ## Derived stats
 *
 * After all analyzers write their results, the engine computes derived values
 * that depend on multiple analyzers' output (e.g. average speed = distance / duration).
 * This removes inter-analyzer ordering dependencies.
 *
 * @see PointAnalyzerInterface for the analyzer lifecycle
 * @see AbstractPointAnalyzer for a convenient base class
 */
class Engine
{
	/** @var PointAnalyzerInterface[] */
	private array $analyzers = [];

	/**
	 * @param bool $sortByTimestamp Sort points by timestamp before analysis.
	 *                              Useful for GPX files with out-of-order points.
	 */
	public function __construct(
		private bool $sortByTimestamp = false,
	) {}

	/**
	 * Register an analyzer to participate in the single-pass computation.
	 *
	 * Analyzers are called in registration order. For most analyzers, order
	 * does not matter — each writes to its own Stats fields. The engine
	 * computes derived stats (speed, pace) after all analyzers finish.
	 *
	 * @return $this Fluent interface
	 */
	public function addAnalyzer(PointAnalyzerInterface $analyzer): self
	{
		$this->analyzers[] = $analyzer;
		return $this;
	}

	/**
	 * Create an engine with the standard set of analyzers.
	 *
	 * This is the recommended way to get started — it registers all built-in
	 * analyzers with sensible defaults. Pass named arguments to customize
	 * specific analyzers' behavior.
	 *
	 * ```php
	 * // All defaults
	 * $engine = Engine::default();
	 *
	 * // Custom elevation smoothing + sorting
	 * $engine = Engine::default(
	 *     sortByTimestamp: true,
	 *     applyElevationSmoothing: true,
	 *     elevationSmoothingThreshold: 3,
	 * );
	 * ```
	 */
	public static function default(
		bool $sortByTimestamp = false,
		bool $applyDistanceSmoothing = false,
		int $distanceSmoothingThreshold = 2,
		bool $ignoreZeroElevation = false,
		bool $applyElevationSmoothing = false,
		int $elevationSmoothingThreshold = 2,
		?int $elevationSmoothingSpikesThreshold = null,
		float $speedThreshold = 0.5,
	): self {
		return (new self(sortByTimestamp: $sortByTimestamp))
			->addAnalyzer(new DistanceAnalyzer(
				applySmoothing: $applyDistanceSmoothing,
				smoothingThreshold: $distanceSmoothingThreshold,
			))
			->addAnalyzer(new ElevationAnalyzer(
				ignoreZeroElevation: $ignoreZeroElevation,
				applySmoothing: $applyElevationSmoothing,
				smoothingThreshold: $elevationSmoothingThreshold,
				spikesThreshold: $elevationSmoothingSpikesThreshold,
			))
			->addAnalyzer(new AltitudeAnalyzer(
				ignoreZeroElevation: $ignoreZeroElevation,
			))
			->addAnalyzer(new TimestampAnalyzer())
			->addAnalyzer(new BoundsAnalyzer())
			->addAnalyzer(new MovementAnalyzer(
				speedThreshold: $speedThreshold,
			))
			->addAnalyzer(new TrackPointExtensionAnalyzer());
	}

	/**
	 * Process the GPX file: optionally sort, then run single-pass analysis.
	 */
	public function process(GpxFile $gpxFile): GpxFile
	{
		if ($this->sortByTimestamp) {
			$this->sortPoints($gpxFile);
		}

		$this->processTracks($gpxFile);
		$this->processRoutes($gpxFile);
		$this->finalizeFile($gpxFile);

		return $gpxFile;
	}

	/**
	 * Process all tracks: segments → points in a single pass per segment,
	 * then aggregate segment stats into track stats.
	 */
	private function processTracks(GpxFile $gpxFile): void
	{
		foreach ($gpxFile->tracks as $track) {
			$track->stats = new Stats();

			if (empty($track->segments)) {
				continue;
			}

			foreach ($track->segments as $segment) {
				$segment->stats = new Stats();

				if (!empty($segment->points)) {
					$this->analyzePoints($segment->points, $segment->stats);
				}
			}

			foreach ($this->analyzers as $analyzer) {
				$analyzer->aggregateTrack($track);
			}

			$this->computeDerivedStats($track->stats);
		}
	}

	/**
	 * Process all routes: points in a single pass per route.
	 */
	private function processRoutes(GpxFile $gpxFile): void
	{
		foreach ($gpxFile->routes as $route) {
			$route->stats = new Stats();

			if (!empty($route->points)) {
				$this->analyzePoints($route->points, $route->stats);
			}
		}
	}

	/**
	 * Run all analyzers over a set of points in a single pass.
	 *
	 * This is the core of the engine — one loop over points, all analyzers
	 * participate simultaneously.
	 *
	 * @param Point[] $points The points to analyze
	 * @param Stats   $stats  The Stats object to populate
	 */
	private function analyzePoints(array $points, Stats $stats): void
	{
		// Phase 1: Reset all analyzers
		foreach ($this->analyzers as $analyzer) {
			$analyzer->begin();
		}

		// Phase 2: Single pass — every analyzer sees every point
		$previous = null;
		foreach ($points as $point) {
			foreach ($this->analyzers as $analyzer) {
				$analyzer->visit($point, $previous);
			}
			$previous = $point;
		}

		// Phase 3: Write results to stats
		foreach ($this->analyzers as $analyzer) {
			$analyzer->end($stats);
		}

		// Phase 4: Compute derived stats (speed, pace) from combined results
		$this->computeDerivedStats($stats);
	}

	/**
	 * Sort points by timestamp within each segment and route.
	 *
	 * Skips segments/routes where the first point has no timestamp
	 * (assumes all points are either timestamped or not).
	 */
	private function sortPoints(GpxFile $gpxFile): void
	{
		$compare = fn($a, $b) => $a->time <=> $b->time;

		foreach ($gpxFile->tracks as $track) {
			foreach ($track->segments as $segment) {
				if (!empty($segment->points) && $segment->points[0]->time !== null) {
					usort($segment->points, $compare);
				}
			}
		}

		foreach ($gpxFile->routes as $route) {
			if (!empty($route->points) && $route->points[0]->time !== null) {
				usort($route->points, $compare);
			}
		}
	}

	/**
	 * File-level finalization — called after all tracks and routes are processed.
	 */
	private function finalizeFile(GpxFile $gpxFile): void
	{
		foreach ($this->analyzers as $analyzer) {
			$analyzer->finalizeFile($gpxFile);
		}
	}

	/**
	 * Compute values that depend on multiple analyzers' output.
	 *
	 * Average speed and pace require both distance (from DistanceAnalyzer)
	 * and duration (from TimestampAnalyzer). Rather than creating ordering
	 * dependencies between analyzers, the engine computes these derived
	 * values after all analyzers have written their results.
	 */
	private function computeDerivedStats(Stats $stats): void
	{
		if ($stats->startedAt instanceof \DateTime && $stats->finishedAt instanceof \DateTime) {
			$stats->duration = abs(
				$stats->finishedAt->getTimestamp() - $stats->startedAt->getTimestamp()
			);

			if ($stats->duration != 0 && $stats->distance !== null) {
				$stats->averageSpeed = $stats->distance / $stats->duration;
			}

			if ($stats->distance !== null && $stats->distance != 0) {
				$stats->averagePace = $stats->duration / ($stats->distance / 1000);
			}
		}

		if ($stats->movingDuration !== null && $stats->movingDuration > 0 && $stats->distance !== null) {
			$stats->movingAverageSpeed = $stats->distance / $stats->movingDuration;
		}
	}
}