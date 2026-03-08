<?php

namespace phpGPX\Models;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Helpers\SerializationHelper;

enum PointType: string
{
	case Waypoint = 'wpt';
	case Trackpoint = 'trkpt';
	case Routepoint = 'rtept';
}

/**
 * GPX point representation according to GPX 1.1 specification.
 * @see http://www.topografix.com/GPX/1/1/#type_wptType
 */
class Point implements \JsonSerializable
{
	/** The latitude of the point. Decimal degrees, WGS84 datum. */
	public ?float $latitude = null;

	/** The longitude of the point. Decimal degrees, WGS84 datum. */
	public ?float $longitude = null;

	/** Elevation (in meters) of the point. */
	public ?float $elevation = null;

	/** Creation/modification timestamp (UTC). */
	public ?\DateTime $time = null;

	/** Magnetic variation (in degrees) at the point. */
	public ?float $magVar = null;

	/** Height (in meters) of geoid above WGS84 earth ellipsoid. */
	public ?float $geoidHeight = null;

	/** The GPS name of the waypoint. */
	public ?string $name = null;

	/** GPS waypoint comment. */
	public ?string $comment = null;

	/** Text description of the element. */
	public ?string $description = null;

	/** Source of data. */
	public ?string $source = null;

	/** @var Link[] Links to additional information about the waypoint. */
	public array $links = [];

	/** Text of GPS symbol name. */
	public ?string $symbol = null;

	/** Type (classification) of the waypoint. */
	public ?string $type = null;

	/** Type of GPS fix. Possible values: none, 2d, 3d, dgps, pps. */
	public ?string $fix = null;

	/** Number of satellites used to calculate the GPX fix. */
	public ?int $satellitesNumber = null;

	/** Horizontal dilution of precision. */
	public ?float $hdop = null;

	/** Vertical dilution of precision. */
	public ?float $vdop = null;

	/** Position dilution of precision. */
	public ?float $pdop = null;

	/** Number of seconds since last DGPS update. */
	public ?int $ageOfGpsData = null;

	/** ID of DGPS station used in differential correction. */
	public ?int $dgpsid = null;

	/** Difference in distance (in meters) from previous point. Computed by phpGPX. */
	public ?float $difference = null;

	/** Distance from collection start in meters. Computed by phpGPX. */
	public ?float $distance = null;

	/** GPX extensions. */
	public ?Extensions $extensions = null;

	public function __construct(
		private readonly PointType $pointType,
	) {}

	public function getPointType(): PointType
	{
		return $this->pointType;
	}

	public function jsonSerialize(): array
	{
		$properties = array_filter([
			'name' => $this->name,
			'ele' => $this->elevation,
			'time' => DateTimeHelper::formatDateTime($this->time),
			'magvar' => $this->magVar,
			'geoidheight' => $this->geoidHeight,
			'cmt' => $this->comment,
			'desc' => $this->description,
			'src' => $this->source,
			'link' => !empty($this->links) ? $this->links : null,
			'sym' => $this->symbol,
			'type' => $this->type,
			'fix' => $this->fix,
			'sat' => $this->satellitesNumber,
			'hdop' => $this->hdop,
			'vdop' => $this->vdop,
			'pdop' => $this->pdop,
			'ageofdgpsdata' => $this->ageOfGpsData,
			'dgpsid' => $this->dgpsid,
			'extensions' => $this->extensions,
		], fn($v) => $v !== null);

		return [
			'type' => 'Feature',
			'geometry' => [
				'type' => 'Point',
				'coordinates' => SerializationHelper::position($this->longitude, $this->latitude, $this->elevation),
			],
			'properties' => $properties ?: new \stdClass(),
		];
	}
}