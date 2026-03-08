<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\DistanceCalculator;
use phpGPX\Helpers\ElevationGainLossCalculator;
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

		list($this->stats->cumulativeElevationGain, $this->stats->cumulativeElevationLoss) =
			ElevationGainLossCalculator::calculate($this->getPoints());

		$calculator = new DistanceCalculator($this->getPoints());
		$this->stats->distance = $calculator->getRawDistance();
		$this->stats->realDistance = $calculator->getRealDistance();

		// Find first/last non-null timestamps (#51)
		for ($i = 0; $i < $pointCount; $i++) {
			if ($this->points[$i]->time instanceof \DateTime) {
				$this->stats->startedAt = $this->points[$i]->time;
				$this->stats->startedAtCoords = ["lat" => $this->points[$i]->latitude, "lng" => $this->points[$i]->longitude];
				break;
			}
		}
		for ($i = $pointCount - 1; $i >= 0; $i--) {
			if ($this->points[$i]->time instanceof \DateTime) {
				$this->stats->finishedAt = $this->points[$i]->time;
				$this->stats->finishedAtCoords = ["lat" => $this->points[$i]->latitude, "lng" => $this->points[$i]->longitude];
				break;
			}
		}

		// Find min/max altitude — don't assume first point (#70)
		for ($i = 0; $i < $pointCount; $i++) {
			$ele = $this->points[$i]->elevation;
			if ($ele === null) {
				continue;
			}
			if (phpGPX::$IGNORE_ELEVATION_0 && $ele == 0) {
				continue;
			}

			$coords = ["lat" => $this->points[$i]->latitude, "lng" => $this->points[$i]->longitude];

			if ($this->stats->maxAltitude === null || $ele > $this->stats->maxAltitude) {
				$this->stats->maxAltitude = $ele;
				$this->stats->maxAltitudeCoords = $coords;
			}
			if ($this->stats->minAltitude === null || $ele < $this->stats->minAltitude) {
				$this->stats->minAltitude = $ele;
				$this->stats->minAltitudeCoords = $coords;
			}
		}

		if ($this->stats->startedAt instanceof \DateTime && $this->stats->finishedAt instanceof \DateTime) {
			$this->stats->duration = $this->stats->finishedAt->getTimestamp() - $this->stats->startedAt->getTimestamp();

			if ($this->stats->duration != 0) {
				$this->stats->averageSpeed = $this->stats->distance / $this->stats->duration;
			}

			if ($this->stats->distance != 0) {
				$this->stats->averagePace = $this->stats->duration / ($this->stats->distance / 1000);
			}
		}
	}
}
