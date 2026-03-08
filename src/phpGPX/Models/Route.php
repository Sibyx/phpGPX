<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;

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
	public array $points;

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
	public function getPoints(): array
	{
		return $this->points;
	}

	public function jsonSerialize(): array
	{
		$coordinates = [];
		foreach ($this->points as $point) {
			$coordinates[] = SerializationHelper::position($point->longitude, $point->latitude, $point->elevation);
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
				'type' => 'LineString',
				'coordinates' => $coordinates,
			],
			'properties' => $properties ?: new \stdClass(),
		];
	}
}