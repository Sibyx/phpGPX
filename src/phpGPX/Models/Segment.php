<?php

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;

/**
 * A Track Segment holds a list of Track Points which are logically connected in order.
 */
class Segment implements \JsonSerializable
{
	/** @var Point[] */
	public array $points = [];

	public ?Extensions $extensions = null;

	public ?Stats $stats = null;

	public function jsonSerialize(): array
	{
		$coordinates = [];
		foreach ($this->points as $point) {
			$coordinates[] = SerializationHelper::position($point->longitude, $point->latitude, $point->elevation);
		}

		$properties = array_filter([
			'extensions' => $this->extensions,
			'stats' => $this->stats,
		], fn ($v) => $v !== null);

		return [
			'type' => 'Feature',
			'geometry' => [
				'type' => 'LineString',
				'coordinates' => $coordinates,
			],
			'properties' => $properties ?: new \stdClass(),
		];
	}

	/** @return Point[] */
	public function getPoints(): array
	{
		return $this->points;
	}
}
