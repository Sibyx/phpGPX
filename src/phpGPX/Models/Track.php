<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;

/**
 * Class Track
 * @package phpGPX\Models
 */
class Track extends Collection
{

	/** @var Segment[] */
	public array $segments = [];


	/**
	 * Return all points in collection.
	 * @return Point[]
	 */
	public function getPoints(): array
	{
		/** @var Point[] $points */
		$points = [];

		foreach ($this->segments as $segment) {
			$points = array_merge($points, $segment->points);
		}

		return $points;
	}

	public function jsonSerialize(): array
	{
		$segmentCoordinates = [];
		foreach ($this->segments as $segment) {
			$coordinates = [];
			foreach ($segment->points as $point) {
				$coordinates[] = SerializationHelper::position($point->longitude, $point->latitude, $point->elevation);
			}
			$segmentCoordinates[] = $coordinates;
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
				'type' => 'MultiLineString',
				'coordinates' => $segmentCoordinates,
			],
			'properties' => $properties ?: new \stdClass(),
		];
	}
}