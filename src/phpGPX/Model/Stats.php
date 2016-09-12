<?php
/**
 * Created            30/08/16 17:12
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


class Stats
{

	/**
	 * @var double
	 */
	public $distance = 0;

	/**
	 * @var double
	 */
	public $averageSpeed = null;

	/**
	 * @var double
	 */
	public $averagePace = null;

	/**
	 * @var int
	 */
	public $minAltitude = null;

	/**
	 * @var int
	 */
	public $maxAltitude = null;

	/**
	 * @var \DateTime
	 */
	public $startedAt = null;

	/**
	 * @var \DateTime
	 */
	public $finishedAt = null;

	/**
	 * @var int
	 */
	public $duration = null;

	/**
	 * Reset all stats
	 */
	public function reset()
	{
		$this->distance = 0;
		$this->averageSpeed = 0;
		$this->averagePace = 0;
		$this->minAltitude = 0;
		$this->maxAltitude = 0;
		$this->startedAt = null;
		$this->finishedAt = null;
	}

}