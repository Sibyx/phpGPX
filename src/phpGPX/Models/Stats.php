<?php
/**
 * Created            30/08/16 17:12
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\phpGPX;

/**
 * Class Stats
 * @package phpGPX\Models
 */
class Stats implements Summarizable
{

	/**
	 * Distance in meters (m)
	 * @var float
	 */
	public $distance = 0;

	/**
	 * Distance in meters (m) including elevation loss/gain
	 * @var float
	 */
	public $realDistance = 0;

	/**
	 * Average speed in meters per second (m/s)
	 * @var float
	 */
	public $averageSpeed = null;

	/**
	 * Average pace in seconds per kilometer (s/km)
	 * @var float
	 */
	public $averagePace = null;

	/**
	 * Minimal altitude in meters (m)
	 * @var int
	 */
	public $minAltitude = null;

	/**
	 * Minimal altitude coordinate
	 * @var [float,float]
	 */
	public $minAltitudeCoords = null;

	/**
	 * Maximal altitude in meters (m)
	 * @var int
	 */
	public $maxAltitude = null;

	/**
	 * Maximal altitude coordinate
	 * @var [float,float]
	 */
	public $maxAltitudeCoords = null;

	/**
	 * Cumulative elevation gain in meters (m)
	 * @var int
	 */
	public $cumulativeElevationGain = null;

	/**
	 * Cumulative elevation loss in meters (m)
	 * @var int
	 */
	public $cumulativeElevationLoss = null;

	/**
	 * Started time
	 * @var \DateTime
	 */
	public $startedAt = null;

	/**
	 * startedAt coordinate
	 * @var [float,float]
	 */
	public $startedAtCoords = null;

	/**
	 * Ending time
	 * @var \DateTime
	 */
	public $finishedAt = null;

	/**
	 * finishedAt coordinate
	 * @var [float,float]
	 */
	public $finishedAtCoords = null;

	/**
	 * Duration is seconds
	 * @var int
	 */
	public $duration = null;

	/**
	 * Reset all stats
	 */
	public function reset()
	{
		$this->distance = null;
		$this->realDistance = null;
		$this->averageSpeed = null;
		$this->averagePace = null;
		$this->minAltitude = null;
		$this->maxAltitude = null;
		$this->minAltitudeCoords = null;
		$this->maxAltitudeCoords = null;
		$this->cumulativeElevationGain = null;
		$this->cumulativeElevationLoss = null;
		$this->startedAt = null;
		$this->startedAtCoords = null;
		$this->finishedAt = null;
		$this->finishedAtCoords = null;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'distance' => (float)$this->distance,
			'realDistance' => (float)$this->realDistance,
			'avgSpeed' => (float)$this->averageSpeed,
			'avgPace' => (float)$this->averagePace,
			'minAltitude' => (float)$this->minAltitude,
			'minAltitudeCoords' => $this->minAltitudeCoords,
			'maxAltitude' => (float)$this->maxAltitude,
			'maxAltitudeCoords' => $this->maxAltitudeCoords,
			'cumulativeElevationGain' => (float)$this->cumulativeElevationGain,
			'cumulativeElevationLoss' => (float)$this->cumulativeElevationLoss,
			'startedAt' => DateTimeHelper::formatDateTime($this->startedAt, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT),
			'startedAtCoords' => $this->startedAtCoords,
			'finishedAt' => DateTimeHelper::formatDateTime($this->finishedAt, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT),
			'finishedAtCoords' => $this->finishedAtCoords,
			'duration' => (float)$this->duration
		];
	}
}
