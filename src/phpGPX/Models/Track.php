<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;
use phpGPX\phpGPX;

/**
 * Class Track
 * @package phpGPX\Models
 */
class Track extends Collection
{

	/**
	 * Array of Track segments
	 * @var Segment[]
	 */
	public array $segments;

	/**
	 * Track constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->segments = [];
	}


	/**
	 * Return all points in collection.
	 * @return Point[]
	 */
	public function getPoints(): array
    {
		/** @var Point[] $points */
		$points = [];

		foreach ($this->segments as $segment) {
			$points = array_merge($points, $segment->points);
		}

		if (phpGPX::$SORT_BY_TIMESTAMP && !empty($points) && $points[0]->time !== null) {
			usort($points, array('phpGPX\Helpers\DateTimeHelper', 'comparePointsByTimestamp'));
		}

		return $points;
	}

	public function jsonSerialize(): array
	{
		$segmentCoordinates = [];
		foreach ($this->segments as $segment) {
			$coordinates = [];
			foreach ($segment->points as $point) {
				$coordinates[] = SerializationHelper::position($point->longitude, $point->latitude, $point->elevation);
			}
			$segmentCoordinates[] = $coordinates;
		}

		$properties = array_filter([
			'name' => $this->name,
			'cmt' => $this->comment,
			'desc' => $this->description,
			'src' => $this->source,
			'link' => !empty($this->links) ? $this->links : null,
			'number' => $this->number,
			'type' => $this->type,
			'extensions' => $this->extensions,
			'stats' => $this->stats,
		], fn($v) => $v !== null);

		return [
			'type' => 'Feature',
			'geometry' => [
				'type' => 'MultiLineString',
				'coordinates' => $segmentCoordinates,
			],
			'properties' => $properties ?: new \stdClass(),
		];
	}

	/**
	 * Recalculate stats objects.
	 * @return void
	 */
	public function recalculateStats(): void
	{
		if (empty($this->stats)) {
			$this->stats = new Stats();
		}

		$this->stats->reset();

		if (empty($this->segments)) {
			return;
		}

		$segmentsCount = count($this->segments);

		for ($s = 0; $s < $segmentsCount; $s++) {
			$this->segments[$s]->recalculateStats();
			$segStats = $this->segments[$s]->stats;

			$this->stats->cumulativeElevationGain += $segStats->cumulativeElevationGain;
			$this->stats->cumulativeElevationLoss += $segStats->cumulativeElevationLoss;
			$this->stats->distance += $segStats->distance;
			$this->stats->realDistance += $segStats->realDistance;

			// Aggregate min/max altitude from segments
			if ($segStats->maxAltitude !== null && ($this->stats->maxAltitude === null || $segStats->maxAltitude > $this->stats->maxAltitude)) {
				$this->stats->maxAltitude = $segStats->maxAltitude;
				$this->stats->maxAltitudeCoords = $segStats->maxAltitudeCoords;
			}
			if ($segStats->minAltitude !== null && ($this->stats->minAltitude === null || $segStats->minAltitude < $this->stats->minAltitude)) {
				$this->stats->minAltitude = $segStats->minAltitude;
				$this->stats->minAltitudeCoords = $segStats->minAltitudeCoords;
			}

			// Aggregate startedAt/finishedAt from segments (#51)
			if ($segStats->startedAt instanceof \DateTime && ($this->stats->startedAt === null || $segStats->startedAt < $this->stats->startedAt)) {
				$this->stats->startedAt = $segStats->startedAt;
				$this->stats->startedAtCoords = $segStats->startedAtCoords;
			}
			if ($segStats->finishedAt instanceof \DateTime && ($this->stats->finishedAt === null || $segStats->finishedAt > $this->stats->finishedAt)) {
				$this->stats->finishedAt = $segStats->finishedAt;
				$this->stats->finishedAtCoords = $segStats->finishedAtCoords;
			}
		}

		if ($this->stats->startedAt instanceof \DateTime && $this->stats->finishedAt instanceof \DateTime) {
			$this->stats->duration = abs($this->stats->finishedAt->getTimestamp() - $this->stats->startedAt->getTimestamp());

			if ($this->stats->duration != 0) {
				$this->stats->averageSpeed = $this->stats->distance / $this->stats->duration;
			}

			if ($this->stats->distance != 0) {
				$this->stats->averagePace = $this->stats->duration / ($this->stats->distance / 1000);
			}
		}
	}
}
