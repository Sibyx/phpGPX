<?php
/**
 * Created            30/08/16 17:12
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\DateTimeHelper;

/**
 * Class Stats
 * @package phpGPX\Models
 */
class Stats implements \JsonSerializable
{

	/**
	 * Distance in meters (m)
	 * @var float|null
	 */
	public ?float $distance = null;

	/**
	 * Distance in meters (m) including elevation loss/gain
	 * @var float|null
	 */
	public ?float $realDistance = null;

	/**
	 * Average speed in meters per second (m/s)
	 * @var float|null
	 */
	public ?float $averageSpeed = null;

	/**
	 * Average pace in seconds per kilometer (s/km)
	 * @var float|null
	 */
	public ?float $averagePace = null;

	/**
	 * Minimal altitude in meters (m)
	 * @var float|null
	 */
	public ?float $minAltitude = null;

	/**
	 * Minimal altitude coordinate
	 * @var array|null
	 */
	public ?array $minAltitudeCoords = null;

	/**
	 * Maximal altitude in meters (m)
	 * @var float|null
	 */
	public ?float $maxAltitude = null;

	/**
	 * Maximal altitude coordinate
	 * @var array|null
	 */
	public ?array $maxAltitudeCoords = null;

	/**
	 * Cumulative elevation gain in meters (m)
	 * @var float|null
	 */
	public ?float $cumulativeElevationGain = null;

	/**
	 * Cumulative elevation loss in meters (m)
	 * @var float|null
	 */
	public ?float $cumulativeElevationLoss = null;

	/**
	 * Started time
	 * @var \DateTime|null
	 */
	public ?\DateTime $startedAt = null;

	/**
	 * startedAt coordinate
	 * @var array|null
	 */
	public ?array $startedAtCoords = null;

	/**
	 * Ending time
	 * @var \DateTime|null
	 */
	public ?\DateTime $finishedAt = null;

	/**
	 * finishedAt coordinate
	 * @var array|null
	 */
	public ?array $finishedAtCoords = null;

	/**
	 * Duration is seconds
	 * @var float|null
	 */
	public ?float $duration = null;

	/**
	 * Coordinate bounds
	 * @var Bounds|null
	 */
	public ?Bounds $bounds = null;

	/**
	 * Moving duration in seconds (excludes stopped time)
	 * @var float|null
	 */
	public ?float $movingDuration = null;

	/**
	 * Average speed while moving in meters per second (m/s)
	 * @var float|null
	 */
	public ?float $movingAverageSpeed = null;

	/**
	 * Average heart rate in beats per minute (bpm)
	 * @var float|null
	 */
	public ?float $averageHeartRate = null;

	/**
	 * Maximum heart rate in beats per minute (bpm)
	 * @var float|null
	 */
	public ?float $maxHeartRate = null;

	/**
	 * Average cadence in revolutions per minute (rpm)
	 * @var float|null
	 */
	public ?float $averageCadence = null;

	/**
	 * Average temperature in degrees Celsius
	 * @var float|null
	 */
	public ?float $averageTemperature = null;

	public function jsonSerialize(): array
	{
		return array_filter([
			'distance' => $this->distance,
			'realDistance' => $this->realDistance,
			'avgSpeed' => $this->averageSpeed,
			'avgPace' => $this->averagePace,
			'minAltitude' => $this->minAltitude,
			'minAltitudeCoords' => $this->minAltitudeCoords,
			'maxAltitude' => $this->maxAltitude,
			'maxAltitudeCoords' => $this->maxAltitudeCoords,
			'cumulativeElevationGain' => $this->cumulativeElevationGain,
			'cumulativeElevationLoss' => $this->cumulativeElevationLoss,
			'startedAt' => DateTimeHelper::formatDateTime($this->startedAt),
			'startedAtCoords' => $this->startedAtCoords,
			'finishedAt' => DateTimeHelper::formatDateTime($this->finishedAt),
			'finishedAtCoords' => $this->finishedAtCoords,
			'duration' => $this->duration,
			'bounds' => $this->bounds?->jsonSerialize(),
			'movingDuration' => $this->movingDuration,
			'movingAvgSpeed' => $this->movingAverageSpeed,
			'avgHeartRate' => $this->averageHeartRate,
			'maxHeartRate' => $this->maxHeartRate,
			'avgCadence' => $this->averageCadence,
			'avgTemperature' => $this->averageTemperature,
		], fn($v) => $v !== null);
	}
}
