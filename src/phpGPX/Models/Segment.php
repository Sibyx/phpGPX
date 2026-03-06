<?php
/**
 * Created            26/08/16 15:26
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;
use phpGPX\Helpers\DistanceCalculator;
use phpGPX\Helpers\ElevationGainLossCalculator;
use phpGPX\Helpers\GeoHelper;
use phpGPX\Helpers\SerializationHelper;
use phpGPX\phpGPX;

/**
 * Class Segment
 * A Track Segment holds a list of Track Points which are logically connected in order.
 * To represent a single GPS track where GPS reception was lost, or the GPS receiver was turned off,
 * start a new Track Segment for each continuous span of track data.
 * @package phpGPX\Models
 */
class Segment implements \JsonSerializable, GpxSerializable, StatsCalculator
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


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'points' => SerializationHelper::serialize($this->points),
			'extensions' => SerializationHelper::serialize($this->extensions),
			'stats' => SerializationHelper::serialize($this->stats)
		];
	}

	/**
	 * Implements JsonSerializable interface
	 * Always returns GeoJSON format
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		// GeoJSON LineString feature
		$coordinates = [];
		$properties = [
			'extensions' => SerializationHelper::serialize($this->extensions)
		];

		// Filter out null values
		$properties = array_filter($properties, function ($value) {
			return $value !== null;
		});

		// Add stats if available
		if ($this->stats) {
			$properties['stats'] = $this->stats->jsonSerialize();
		}

		// Collect coordinates from segment points
		foreach ($this->points as $point) {
			$coordinates[] = [
				(float) $point->longitude,
				(float) $point->latitude,
				SerializationHelper::floatOrNull($point->elevation)
			];
		}

		return [
			'type' => 'Feature',
			'geometry' => [
				'type' => 'LineString',
				'coordinates' => $coordinates
			],
			'properties' => $properties
		];
	}

	/**
	 * GPX serializer
	 * @param \SimpleXMLElement $node
	 * @return void
	 */
	public static function gpxSerialize(\SimpleXMLElement $node): void
	{
		// Implementation required by GpxSerializable interface
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation required by GpxSerializable interface
	}


	/**
	 * @return array|Point[]
	 */
	public function getPoints(): array
	{
		return $this->points;
	}

	/**
	 * Recalculate stats objects.
	 * @return void
	 */
	public function recalculateStats(): void
	{
		if (empty($this->stats)) {
			$this->stats = new Stats();
		}

		$count = count($this->points);
		$this->stats->reset();

		if (empty($this->points)) {
			return;
		}

		$firstPoint = &$this->points[0];
		$lastPoint = end($this->points);

		$this->stats->startedAt = $firstPoint->time;
		$this->stats->startedAtCoords = ["lat" => $firstPoint->latitude, "lng" => $firstPoint->longitude];
		$this->stats->finishedAt = $lastPoint->time;
		$this->stats->finishedAtCoords = ["lat" => $lastPoint->latitude, "lng" => $lastPoint->longitude];
		$this->stats->minAltitude = $firstPoint->elevation;
		$this->stats->minAltitudeCoords = ["lat" => $firstPoint->latitude, "lng" => $firstPoint->longitude];

		list($this->stats->cumulativeElevationGain, $this->stats->cumulativeElevationLoss) =
			ElevationGainLossCalculator::calculate($this->getPoints());

		$calculator = new DistanceCalculator($this->getPoints());
		$this->stats->distance = $calculator->getRawDistance();
		$this->stats->realDistance = $calculator->getRealDistance();

		for ($i = 0; $i < $count; $i++) {
			if ($this->stats->maxAltitude < $this->points[$i]->elevation) {
				$this->stats->maxAltitude = $this->points[$i]->elevation;
				$this->stats->maxAltitudeCoords = ["lat" => $this->points[$i]->latitude, "lng" => $this->points[$i]->longitude];
			}

			if ((phpGPX::$IGNORE_ELEVATION_0 === false || $this->points[$i]->elevation > 0) && $this->stats->minAltitude > $this->points[$i]->elevation) {
				$this->stats->minAltitude = $this->points[$i]->elevation;
				$this->stats->minAltitudeCoords = ["lat" => $this->points[$i]->latitude, "lng" => $this->points[$i]->longitude];
			}
		}

		if (isset($firstPoint->time) && isset($lastPoint->time) && $firstPoint->time instanceof \DateTime && $lastPoint->time instanceof \DateTime) {
			$this->stats->duration = $lastPoint->time->getTimestamp() - $firstPoint->time->getTimestamp();

			if ($this->stats->duration != 0) {
				$this->stats->averageSpeed = $this->stats->distance / $this->stats->duration;
			}

			if ($this->stats->distance != 0) {
				$this->stats->averagePace = $this->stats->duration / ($this->stats->distance / 1000);
			}
		}
	}
}
