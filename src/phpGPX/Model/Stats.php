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

}