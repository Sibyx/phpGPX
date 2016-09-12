<?php
/**
 * Created            26/08/16 15:26
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


use phpGPX\Helpers\Utils;

class Segment implements Summarizable
{

	/**
	 * Array of segment points
	 * @var Point[]
	 */
	public $points;

	/**
	 * @var Stats
	 */
	public $stats;

	/**
	 * Segment constructor.
	 */
	public function __construct()
	{
		$this->stats = new Stats();
	}

	/**
	 * @return array
	 */
	function summary()
	{
		return [
			'points' => Utils::serialize($this->points),
			'stats' => $this->summary()
		];
	}

	/**
	 * Return valid XML node based on GPX standard and Garmin Extensions
	 * @return mixed
	 */
	function toNode()
	{
		return null;
	}
}