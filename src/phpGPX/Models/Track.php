<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\GeoHelper;
use phpGPX\Helpers\SerializationHelper;
use phpGPX\phpGPX;

/**
 * Class Track
 * @package phpGPX\Models
 */
class Track extends Collection implements \phpGPX\GpxSerializable
{

	/**
	 * Array of Track segments
	 * @var Segment[]
	 */
	public array $segments;

	/**
	 * Track constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->segments = [];
	}


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
		// GeoJSON MultiLineString feature
		$segmentCoordinates = [];
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

		// Collect coordinates from track segments
		foreach ($this->segments as $segment) {
			$coordinates = [];

			foreach ($segment->points as $point) {
				$coordinates[] = [
					(float) $point->longitude,
					(float) $point->latitude,
					SerializationHelper::floatOrNull($point->elevation)
				];
			}

			$segmentCoordinates[] = $coordinates;
		}

		return [
			'type' => 'Feature',
			'geometry' => [
				'type' => 'MultiLineString',
				'coordinates' => $segmentCoordinates
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
		// This method would be called to serialize a Track to GPX XML
		// Since TrackParser already handles this, this method can be empty
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation required by GpxSerializable interface
		// This method would be called to deserialize GPX XML to a Track
		// Since TrackParser already handles this, this method can be empty
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
			'trkseg' => SerializationHelper::serialize($this->segments),
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

		if (empty($this->segments)) {
			return;
		}

		$segmentsCount = count($this->segments);

		$firstSegment = null;
		$firstPoint = null;

		// Identify first Segment/Point
		for ($s = 0; $s < $segmentsCount; $s++) {
			$pointCount = count($this->segments[$s]->points);
			for ($p = 0; $p < $pointCount; $p++) {
				if (is_null($firstPoint)) {
					$firstPoint = &$this->segments[$s]->points[$p];
					$firstSegment = &$this->segments[$s];
					break;
				}
			}
		}

		if (empty($firstPoint)) {
			return;
		}

		$lastSegment = end($this->segments);
		$lastPoint = end(end($this->segments)->points);

		$this->stats->startedAt = $firstPoint->time;
		$this->stats->startedAtCoords = ["lat" => $firstPoint->latitude, "lng" => $firstPoint->longitude];
		$this->stats->finishedAt = $lastPoint->time;
		$this->stats->finishedAtCoords = ["lat" => $lastPoint->latitude, "lng" => $lastPoint->longitude];
		$this->stats->minAltitude = $firstPoint->elevation;
		$this->stats->minAltitudeCoords = ["lat" => $firstPoint->latitude, "lng" => $firstPoint->longitude];

		for ($s = 0; $s < $segmentsCount; $s++) {
			$this->segments[$s]->recalculateStats();

			$this->stats->cumulativeElevationGain += $this->segments[$s]->stats->cumulativeElevationGain;
			$this->stats->cumulativeElevationLoss += $this->segments[$s]->stats->cumulativeElevationLoss;

			$this->stats->distance += $this->segments[$s]->stats->distance;
			$this->stats->realDistance += $this->segments[$s]->stats->realDistance;

			if ($this->stats->minAltitude === null) {
				$this->stats->minAltitude = $this->segments[$s]->stats->minAltitude;
				$this->stats->minAltitudeCoords = $this->segments[$s]->stats->minAltitudeCoords;
			}
			if ($this->stats->maxAltitude < $this->segments[$s]->stats->maxAltitude) {
				$this->stats->maxAltitude = $this->segments[$s]->stats->maxAltitude;
				$this->stats->maxAltitudeCoords = $this->segments[$s]->stats->maxAltitudeCoords;
			}
			if ($this->stats->minAltitude > $this->segments[$s]->stats->minAltitude) {
				$this->stats->minAltitude = $this->segments[$s]->stats->minAltitude;
				$this->stats->minAltitudeCoords = $this->segments[$s]->stats->minAltitudeCoords;
			}
		}

		if (($firstPoint->time instanceof \DateTime) && ($lastPoint->time instanceof \DateTime)) {
			$this->stats->duration = abs($lastPoint->time->getTimestamp() - $firstPoint->time->getTimestamp());

			if ($this->stats->duration != 0) {
				$this->stats->averageSpeed = $this->stats->distance / $this->stats->duration;
			}

			if ($this->stats->distance != 0) {
				$this->stats->averagePace = $this->stats->duration / ($this->stats->distance / 1000);
			}
		}
	}
}
