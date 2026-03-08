<?php
/**
 * Created            26/08/16 15:26
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Config;
use phpGPX\Helpers\DistanceCalculator;
use phpGPX\Helpers\ElevationGainLossCalculator;
use phpGPX\Helpers\SerializationHelper;

/**
 * Class Segment
 * A Track Segment holds a list of Track Points which are logically connected in order.
 * To represent a single GPS track where GPS reception was lost, or the GPS receiver was turned off,
 * start a new Track Segment for each continuous span of track data.
 * @package phpGPX\Models
 */
class Segment implements \JsonSerializable, StatsCalculator
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
	public function recalculateStats(Config $config): void
	{
		if (empty($this->stats)) {
			$this->stats = new Stats();
		}

		$count = count($this->points);
		$this->stats->reset();

		if (empty($this->points)) {
			return;
		}

		list($this->stats->cumulativeElevationGain, $this->stats->cumulativeElevationLoss) =
			ElevationGainLossCalculator::calculate($this->getPoints(), $config);

		$calculator = new DistanceCalculator($this->getPoints(), $config);
		$this->stats->distance = $calculator->getRawDistance();
		$this->stats->realDistance = $calculator->getRealDistance();

		// Find first/last non-null timestamps (#51)
		for ($i = 0; $i < $count; $i++) {
			if ($this->points[$i]->time instanceof \DateTime) {
				$this->stats->startedAt = $this->points[$i]->time;
				$this->stats->startedAtCoords = ["lat" => $this->points[$i]->latitude, "lng" => $this->points[$i]->longitude];
				break;
			}
		}
		for ($i = $count - 1; $i >= 0; $i--) {
			if ($this->points[$i]->time instanceof \DateTime) {
				$this->stats->finishedAt = $this->points[$i]->time;
				$this->stats->finishedAtCoords = ["lat" => $this->points[$i]->latitude, "lng" => $this->points[$i]->longitude];
				break;
			}
		}

		// Find min/max altitude — don't assume first point (#70)
		for ($i = 0; $i < $count; $i++) {
			$ele = $this->points[$i]->elevation;
			if ($ele === null) {
				continue;
			}
			if ($config->ignoreZeroElevation && $ele == 0) {
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