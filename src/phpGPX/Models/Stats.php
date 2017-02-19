<?php
/**
 * Created            30/08/16 17:12
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;


use phpGPX\Helpers\DateTimeHelper;
use phpGPX\phpGPX;

class Stats implements Summarizable
{

	/**
	 * Distance in meters (m)
	 * @var double
	 */
	public $distance = 0;

	/**
	 * Average speed in meters per second (m/s)
	 * @var double
	 */
	public $averageSpeed = null;

	/**
	 * Average pace in seconds per kilometer (s/km)
	 * @var double
	 */
	public $averagePace = null;

	/**
	 * Minimal altitude in meters (m)
	 * @var int
	 */
	public $minAltitude = null;

	/**
	 * Maximal altitude in meters (m)
	 * @var int
	 */
	public $maxAltitude = null;

	/**
	 * Started time
	 * @var \DateTime
	 */
	public $startedAt = null;

	/**
	 * Ending time
	 * @var \DateTime
	 */
	public $finishedAt = null;

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
		$this->averageSpeed = null;
		$this->averagePace = null;
		$this->minAltitude = null;
		$this->maxAltitude = null;
		$this->startedAt = null;
		$this->finishedAt = null;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	function toArray()
	{
		return [
			'distance' => (double) $this->distance,
			'avgSpeed' => (double) $this->averageSpeed,
			'avgPace' => (double) $this->averagePace,
			'minAltitude' => (double) $this->minAltitude,
			'maxAltitude' => (double) $this->maxAltitude,
			'startedAt' => DateTimeHelper::formatDateTime($this->startedAt, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT),
			'finishedAt' => DateTimeHelper::formatDateTime($this->finishedAt, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT),
			'duration' => (double) $this->duration
		];
	}
}