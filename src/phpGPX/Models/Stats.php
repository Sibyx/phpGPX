<?php
/**
 * Created            30/08/16 17:12
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\GpxSerializable;
use phpGPX\Helpers\DateTimeHelper;
use phpGPX\phpGPX;

/**
 * Class Stats
 * @package phpGPX\Models
 */
class Stats implements \JsonSerializable, GpxSerializable
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
	 * Reset all stats
	 * @return void
	 */
	public function reset(): void
	{
		$this->distance = null;
		$this->realDistance = null;
		$this->averageSpeed = null;
		$this->averagePace = null;
		$this->minAltitude = null;
		$this->maxAltitude = null;
		$this->minAltitudeCoords = null;
		$this->maxAltitudeCoords = null;
		$this->cumulativeElevationGain = null;
		$this->cumulativeElevationLoss = null;
		$this->startedAt = null;
		$this->startedAtCoords = null;
		$this->finishedAt = null;
		$this->finishedAtCoords = null;
		$this->duration = null;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'distance' => $this->distance !== null ? (float)$this->distance : null,
			'realDistance' => $this->realDistance !== null ? (float)$this->realDistance : null,
			'avgSpeed' => $this->averageSpeed !== null ? (float)$this->averageSpeed : null,
			'avgPace' => $this->averagePace !== null ? (float)$this->averagePace : null,
			'minAltitude' => $this->minAltitude !== null ? (float)$this->minAltitude : null,
			'minAltitudeCoords' => $this->minAltitudeCoords,
			'maxAltitude' => $this->maxAltitude !== null ? (float)$this->maxAltitude : null,
			'maxAltitudeCoords' => $this->maxAltitudeCoords,
			'cumulativeElevationGain' => $this->cumulativeElevationGain !== null ? (float)$this->cumulativeElevationGain : null,
			'cumulativeElevationLoss' => $this->cumulativeElevationLoss !== null ? (float)$this->cumulativeElevationLoss : null,
			'startedAt' => $this->startedAt !== null ? DateTimeHelper::formatDateTime($this->startedAt, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT) : null,
			'startedAtCoords' => $this->startedAtCoords,
			'finishedAt' => $this->finishedAt !== null ? DateTimeHelper::formatDateTime($this->finishedAt, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT) : null,
			'finishedAtCoords' => $this->finishedAtCoords,
			'duration' => $this->duration !== null ? (float)$this->duration : null
		];
	}

	/**
	 * Implements JsonSerializable interface
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
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
}
