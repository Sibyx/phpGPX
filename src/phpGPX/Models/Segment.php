<?php
/**
 * Created            26/08/16 15:26
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;


use phpGPX\Helpers\Utils;

class Segment implements Summarizable
{

	/**
	 * Type of the segment (parent collation type (ROUTE|WAYPOINT|TRACK))
	 * @var string
	 */
	private $segmentType;

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
	 * @param string $segmentType
	 */
	public function __construct($segmentType)
	{
		$this->stats = new Stats();
		$this->segmentType = $segmentType;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	function summary()
	{
		return [
			'points' => Utils::serialize($this->points),
			'stats' => $this->summary()
		];
	}
}