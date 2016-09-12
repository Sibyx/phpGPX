<?php
/**
 * Created            26/08/16 14:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;


use phpGPX\Helpers\Utils;
use phpGPX\phpGPX;

class Collection implements Summarizable
{

	const TRACK_COLLECTION = 'track';
	const WAYPOINT_COLLECTION = 'waypoint';
	const ROUTE_COLLECTION = 'route';

	/**
	 * Name of collection if defined
	 * @var string
	 */
	public $name;

	/**
	 * Type of collection
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $collectionType;

	/**
	 * Url of original data source
	 * @var array
	 */
	public $url;

	/**
	 * Data source name (data origin)
	 * @var string
	 */
	public $source;

	/**
	 * Segments array
	 * @var Segment[]
	 */
	public $segments = [];

	/**
	 * @var Stats
	 */
	public $stats;

	/**
	 * Collection constructor.
	 * @param string $collectionType
	 */
	public function __construct($collectionType)
	{
		$this->stats = new Stats();
		$this->collectionType = $collectionType;
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

	/**
	 * Serialize object to array
	 * @return array
	 */
	function summary()
	{
		return [
			'name' => $this->name,
			'type' => $this->type,
			'url' => $this->url,
			'source' => $this->source,
			'segments' => Utils::serialize($this->segments),
			'stats' => $this->stats->summary()
		];
	}
}