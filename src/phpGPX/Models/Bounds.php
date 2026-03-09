<?php

namespace phpGPX\Models;

/**
 * Two lat/lon pairs defining the extent of an element.
 */
class Bounds implements \JsonSerializable
{
	public const TAG_NAME = 'bounds';

	public function __construct(
		public ?float $minLatitude = null,
		public ?float $minLongitude = null,
		public ?float $maxLatitude = null,
		public ?float $maxLongitude = null,
	) {
	}

	/**
	 * GeoJSON bbox: [minLon, minLat, maxLon, maxLat]
	 */
	public function jsonSerialize(): array
	{
		return [$this->minLongitude, $this->minLatitude, $this->maxLongitude, $this->maxLatitude];
	}

	public static function parse(\SimpleXMLElement $node): ?Bounds
	{
		if ($node->getName() != self::TAG_NAME) {
			return null;
		}

		return new Bounds(
			(float) $node['minlat'],
			(float) $node['minlon'],
			(float) $node['maxlat'],
			(float) $node['maxlon'],
		);
	}
}
