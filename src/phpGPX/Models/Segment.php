<?php
/**
 * Created            26/08/16 15:26
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;

/**
 * Class Segment
 * A Track Segment holds a list of Track Points which are logically connected in order.
 * To represent a single GPS track where GPS reception was lost, or the GPS receiver was turned off,
 * start a new Track Segment for each continuous span of track data.
 * @package phpGPX\Models
 */
class Segment implements \JsonSerializable
{
	/**
	 * Array of segment points
	 * @var Point[]
	 */
	public array $points;

	/**
	 * You can add extend GPX by adding your own elements from another schema here.
	 * @var Extensions|null
	 */
	public ?Extensions $extensions;

	/**
	 * @var Stats|null
	 */
	public ?Stats $stats;

	/**
	 * Segment constructor.
	 */
	public function __construct()
	{
		$this->points = [];
		$this->extensions = null;
		$this->stats = null;
	}


	public function jsonSerialize(): array
	{
		$coordinates = [];
		foreach ($this->points as $point) {
			$coordinates[] = SerializationHelper::position($point->longitude, $point->latitude, $point->elevation);
		}

		$properties = array_filter([
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

	/**
	 * @return Point[]
	 */
	public function getPoints(): array
	{
		return $this->points;
	}
}