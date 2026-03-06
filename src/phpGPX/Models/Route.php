<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\DistanceCalculator;
use phpGPX\Helpers\ElevationGainLossCalculator;
use phpGPX\Helpers\GeoHelper;
use phpGPX\Helpers\SerializationHelper;
use phpGPX\phpGPX;

/**
 * Class Route
 * @package phpGPX\Models
 */
class Route extends Collection implements \phpGPX\GpxSerializable
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
		/** @var Point[] $points */
		$points = [];

		$points = array_merge($points, $this->points);

		if (phpGPX::$SORT_BY_TIMESTAMP && !empty($points) && $points[0]->time !== null) {
			usort($points, array('phpGPX\Helpers\DateTimeHelper', 'comparePointsByTimestamp'));
		}

		return $points;
	}

	/**
	 * Serialize object to array for JSON encoding
	 * Always returns GeoJSON format
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		// GeoJSON LineString feature
		$coordinates = [];
		$properties = [
			'name' => SerializationHelper::stringOrNull($this->name),
			'cmt' => SerializationHelper::stringOrNull($this->comment),
			'desc' => SerializationHelper::stringOrNull($this->description),
			'src' => SerializationHelper::stringOrNull($this->source),
			'link' => SerializationHelper::serialize($this->links),
			'number' => SerializationHelper::integerOrNull($this->number),
			'type' => SerializationHelper::stringOrNull($this->type),
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

		// Collect coordinates from route points
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
		// This method would be called to serialize a Route to GPX XML
		// Since RouteParser already handles this, this method can be empty
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation required by GpxSerializable interface
		// This method would be called to deserialize GPX XML to a Route
		// Since RouteParser already handles this, this method can be empty
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'name' => SerializationHelper::stringOrNull($this->name),
			'cmt' => SerializationHelper::stringOrNull($this->comment),
			'desc' => SerializationHelper::stringOrNull($this->description),
			'src' => SerializationHelper::stringOrNull($this->source),
			'link' => SerializationHelper::serialize($this->links),
			'number' => SerializationHelper::integerOrNull($this->number),
			'type' => SerializationHelper::stringOrNull($this->type),
			'extensions' => SerializationHelper::serialize($this->extensions),
			'rtep' => SerializationHelper::serialize($this->points),
			'stats' => SerializationHelper::serialize($this->stats)
		];
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

		$this->stats->reset();

		if (empty($this->points)) {
			return;
		}

		$pointCount = count($this->points);

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

		for ($p = 0; $p < $pointCount; $p++) {
			if ((phpGPX::$IGNORE_ELEVATION_0 === false || $this->points[$p]->elevation > 0) && $this->stats->minAltitude > $this->points[$p]->elevation) {
				$this->stats->minAltitude = $this->points[$p]->elevation;
				$this->stats->minAltitudeCoords = ["lat" => $this->points[$p]->latitude, "lng" => $this->points[$p]->longitude];
			}

			if ($this->stats->maxAltitude < $this->points[$p]->elevation) {
				$this->stats->maxAltitude = $this->points[$p]->elevation;
				$this->stats->maxAltitudeCoords = ["lat" => $this->points[$p]->latitude, "lng" => $this->points[$p]->longitude];
			}

			if ($this->stats->minAltitude > $this->points[$p]->elevation) {
				$this->stats->minAltitude = $this->points[$p]->elevation;
				$this->stats->minAltitudeCoords = ["lat" => $this->points[$p]->latitude, "lng" => $this->points[$p]->longitude];
			}
		}

		if (($firstPoint->time instanceof \DateTime) && ($lastPoint->time instanceof \DateTime)) {
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
