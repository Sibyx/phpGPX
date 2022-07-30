<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\DistanceCalculator;
use phpGPX\Helpers\ElevationGainLossCalculator;
use phpGPX\Helpers\GeoHelper;
use phpGPX\Helpers\SerializationHelper;
use phpGPX\phpGPX;

/**
 * Class Route
 * @package phpGPX\Models
 */
class Route extends Collection
{

	/**
	 * A list of route points.
	 * An original GPX 1.1 attribute.
	 * @var Point[]
	 */
	public $points;

	/**
	 * Route constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->points = [];
	}


	/**
	 * Return all points in collection.
	 * @return Point[]
	 */
	public function getPoints()
	{
		/** @var Point[] $points */
		$points = [];

		$points = array_merge($points, $this->points);

		if (phpGPX::$SORT_BY_TIMESTAMP && !empty($points) && $points[0]->time !== null) {
			usort($points, array('phpGPX\Helpers\DateTimeHelper', 'comparePointsByTimestamp'));
		}

		return $points;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'name' => SerializationHelper::stringOrNull($this->name),
			'cmt' => SerializationHelper::stringOrNull($this->comment),
			'desc' => SerializationHelper::stringOrNull($this->description),
			'src' => SerializationHelper::stringOrNull($this->source),
			'link' => SerializationHelper::serialize($this->links),
			'number' => SerializationHelper::integerOrNull($this->number),
			'type' => SerializationHelper::stringOrNull($this->type),
			'extensions' => SerializationHelper::serialize($this->extensions),
			'rtep' => SerializationHelper::serialize($this->points),
			'stats' => SerializationHelper::serialize($this->stats)
		];
	}

	/**
	 * Recalculate stats objects.
	 * @return void
	 */
	public function recalculateStats()
	{
		if (empty($this->stats)) {
			$this->stats = new Stats();
		}

		$this->stats->reset();

		if (empty($this->points)) {
			return;
		}

		$pointCount = count($this->points);

		$firstPoint = &$this->points[0];
		$lastPoint = end($this->points);

		$this->stats->startedAt = $firstPoint->time;
		$this->stats->finishedAt = $lastPoint->time;
		$this->stats->minAltitude = $firstPoint->elevation;

		list($this->stats->cumulativeElevationGain, $this->stats->cumulativeElevationLoss) =
			ElevationGainLossCalculator::calculate($this->getPoints());

		$calculator = new DistanceCalculator($this->getPoints());
		$this->stats->distance = $calculator->getRawDistance();
		$this->stats->realDistance = $calculator->getRealDistance();

		for ($p = 0; $p < $pointCount; $p++) {
			if ((phpGPX::$IGNORE_ELEVATION_0 === false || $this->points[$p]->elevation > 0) && $this->stats->minAltitude > $this->points[$p]->elevation) {
				$this->stats->minAltitude = $this->points[$p]->elevation;
			}

			if ($this->stats->maxAltitude < $this->points[$p]->elevation) {
				$this->stats->maxAltitude = $this->points[$p]->elevation;
			}

			if ($this->stats->minAltitude > $this->points[$p]->elevation) {
				$this->stats->minAltitude = $this->points[$p]->elevation;
			}
		}

		if (($firstPoint->time instanceof \DateTime) && ($lastPoint->time instanceof \DateTime)) {
			$this->stats->duration = $lastPoint->time->getTimestamp() - $firstPoint->time->getTimestamp();

			if ($this->stats->duration != 0) {
				$this->stats->averageSpeed = $this->stats->distance / $this->stats->duration;
			}

			if ($this->stats->distance != 0) {
				$this->stats->averagePace = $this->stats->duration / ($this->stats->distance / 1000);
			}
		}
	}
}
