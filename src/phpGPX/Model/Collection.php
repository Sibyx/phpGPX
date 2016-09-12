<?php
/**
 * Created            26/08/16 14:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


use phpGPX\Helpers\Utils;
use phpGPX\phpGPX;

class Collection
{

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $source;

	/**
	 * @var Segment[]
	 */
	public $segments = [];

	/**
	 * @var Stats
	 */
	public $stats;

	/**
	 * Collection constructor.
	 */
	public function __construct()
	{
		$this->stats = new Stats();
	}

	public function getPoints()
	{
		/** @var Point[] $points */
		$points = [];

		foreach ($this->segments as $segment)
		{
			$points = array_merge($points, $segment->points);
		}

		if (phpGPX::$SORT_BY_TIMESTAMP && !empty($points) && ($points[0]->timestamp instanceof \DateTime))
		{
			usort($points, array(Utils::class, 'comparePointsByTimestamp'));
		}

		return $points;
	}
}