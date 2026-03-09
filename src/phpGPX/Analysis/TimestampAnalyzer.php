<?php

namespace phpGPX\Analysis;

use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

/**
 * Records first and last timestamps with coordinates.
 *
 * Scans for the first and last points that have a non-null `time` property.
 * Does not assume boundary points have timestamps (#51).
 *
 * ## Derived stats
 *
 * Duration, average speed, and average pace are **not** computed by this
 * analyzer — they depend on distance (from {@see DistanceAnalyzer}) and are
 * computed by the {@see Engine} after all analyzers finish.
 *
 * ## Track aggregation
 *
 * Track start = earliest segment start. Track end = latest segment end.
 */
class TimestampAnalyzer extends AbstractPointAnalyzer
{
	private ?\DateTime $startedAt;
	private ?array $startedAtCoords;
	private ?\DateTime $finishedAt;
	private ?array $finishedAtCoords;

	public function begin(): void
	{
		$this->startedAt = null;
		$this->startedAtCoords = null;
		$this->finishedAt = null;
		$this->finishedAtCoords = null;
	}

	public function visit(Point $current, ?Point $previous): void
	{
		if (!$current->time instanceof \DateTime) {
			return;
		}

		$coords = ['lat' => $current->latitude, 'lng' => $current->longitude];

		// First non-null timestamp becomes startedAt
		if ($this->startedAt === null) {
			$this->startedAt = $current->time;
			$this->startedAtCoords = $coords;
		}

		// Every non-null timestamp updates finishedAt (last one wins)
		$this->finishedAt = $current->time;
		$this->finishedAtCoords = $coords;
	}

	public function end(Stats $stats): void
	{
		$stats->startedAt = $this->startedAt;
		$stats->startedAtCoords = $this->startedAtCoords;
		$stats->finishedAt = $this->finishedAt;
		$stats->finishedAtCoords = $this->finishedAtCoords;
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

			if ($segment->stats->startedAt instanceof \DateTime
				&& ($track->stats->startedAt === null || $segment->stats->startedAt < $track->stats->startedAt)) {
				$track->stats->startedAt = $segment->stats->startedAt;
				$track->stats->startedAtCoords = $segment->stats->startedAtCoords;
			}

			if ($segment->stats->finishedAt instanceof \DateTime
				&& ($track->stats->finishedAt === null || $segment->stats->finishedAt > $track->stats->finishedAt)) {
				$track->stats->finishedAt = $segment->stats->finishedAt;
				$track->stats->finishedAtCoords = $segment->stats->finishedAtCoords;
			}
		}
	}
}
