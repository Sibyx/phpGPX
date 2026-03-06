<?php
/**
 * Created            26/08/16 14:22
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;
use phpGPX\Helpers\DateTimeHelper;
use phpGPX\phpGPX;

enum PointType: string
{
    case waypoint = 'wpt';
    case trackpoint = 'trkpt';
    case routepoint = 'rtept';
}

/**
 * Class Point
 * GPX point representation according to GPX 1.1 specification.
 * @see http://www.topografix.com/GPX/1/1/#type_wptType
 * @package phpGPX\Models
 */
class Point implements \JsonSerializable, \phpGPX\GpxSerializable
{
	const WAYPOINT = 'waypoint';
	const TRACKPOINT = 'track';
	const ROUTEPOINT = 'route';

	/**
	 * The latitude of the point. Decimal degrees, WGS84 datum.
	 * Original GPX 1.1 attribute.
	 * @var float|null
	 */
	public ?float $latitude;

	/**
	 * The longitude of the point. Decimal degrees, WGS84 datum.
	 * Original GPX 1.1 attribute.
	 * @var float|null
	 */
	public ?float $longitude;

	/**
	 * Elevation (in meters) of the point.
	 * Original GPX 1.1 attribute.
	 * @var float|null
	 */
	public ?float $elevation;

	/**
	 * Creation/modification timestamp for element. Date and time in are in Univeral Coordinated Time (UTC), not local time!
	 * Fractional seconds are allowed for millisecond timing in tracklogs.
	 * @var \DateTime|null
	 */
	public ?\DateTime $time;

	/**
	 * Magnetic variation (in degrees) at the point
	 * Original GPX 1.1 attribute.
	 * @var float|null
	 */
	public ?float $magVar;

	/**
	 * Height (in meters) of geoid (mean sea level) above WGS84 earth ellipsoid. As defined in NMEA GGA message.
	 * Original GPX 1.1 attribute.
	 * @var float|null
	 */
	public ?float $geoidHeight;

	/**
	 * The GPS name of the waypoint. This field will be transferred to and from the GPS.
	 * GPX does not place restrictions on the length of this field or the characters contained in it.
	 * It is up to the receiving application to validate the field before sending it to the GPS.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public ?string $name;

	/**
	 * GPS waypoint comment. Sent to GPS as comment.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public ?string $comment;

	/**
	 * A text description of the element. Holds additional information about the element intended for the user, not the GPS.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public ?string $description;

	/**
	 * Source of data. Included to give user some idea of reliability and accuracy of data. "Garmin eTrex", "USGS quad Boston North", e.g.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public ?string $source;

	/**
	 * Link to additional information about the waypoint.
	 * Original GPX 1.1 attribute.
	 * @var Link[]
	 */
	public array $links;

	/**
	 * Text of GPS symbol name. For interchange with other programs, use the exact spelling of the symbol as displayed on the GPS.
	 * If the GPS abbreviates words, spell them out.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public ?string $symbol;

	/**
	 * Type (classification) of the waypoint.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public ?string $type;

	/**
	 * Type of GPS fix. none means GPS had no fix. To signify "the fix info is unknown, leave out fixType entirely. pps = military signal used
	 * Possible values: {'none'|'2d'|'3d'|'dgps'|'pps'}
	 * Original GPX 1.1 attribute.
	 * @see http://www.topografix.com/GPX/1/1/#type_fixType
	 * @var string|null
	 */
	public ?string $fix;

	/**
	 * Number of satellites used to calculate the GPX fix. Always positive value.
	 * Original GPX 1.1 attribute.
	 * @var integer|null
	 */
	public ?int $satellitesNumber;

	/**
	 * Horizontal dilution of precision.
	 * Original GPX 1.1 attribute.
	 * @var float|null
	 */
	public ?float $hdop;

	/**
	 * Vertical dilution of precision.
	 * Original GPX 1.1 attribute.
	 * @var float|null
	 */
	public ?float $vdop;

	/**
	 * Position dilution of precision.
	 * Original GPX 1.1 attribute
	 * @var float|null
	 */
	public ?float $pdop;

	/**
	 * Number of seconds since last DGPS update.
	 * Original GPX 1.1 attribute.
	 * @var integer|null
	 */
	public ?int $ageOfGpsData;

	/**
	 * ID of DGPS station used in differential correction.
	 * Original GPX 1.1 attribute.
	 * @see http://www.topografix.com/GPX/1/1/#type_dgpsStationType
	 * @var integer|null
	 */
	public ?int $dgpsid;

	/**
	 * Difference in in distance (in meters) between last point.
	 * Value is created by phpGPX library.
	 * @var float|null
	 */
	public ?float $difference;

	/**
	 * Distance from collection start in meters.
	 * Value is created by phpGPX library.
	 * @var float|null
	 */
	public ?float $distance;

	/**
	 * Objects stores GPX extensions from another namespaces.
	 * @var Extensions|null
	 */
	public ?Extensions $extensions;

	/**
	 * Type of the point (parent collation type (ROUTE|WAYPOINT|TRACK))
	 * @var string
	 */
	private string $pointType;

	/**
	 * Point constructor.
	 * @param string $pointType
	 */
	public function __construct(string $pointType)
	{
		$this->latitude = null;
		$this->longitude = null;
		$this->elevation = null;
		$this->time = null;
		$this->magVar = null;
		$this->geoidHeight = null;
		$this->name = null;
		$this->comment = null;
		$this->description = null;
		$this->source = null;
		$this->links = [];
		$this->symbol = null;
		$this->type = null;
		$this->fix = null;
		$this->satellitesNumber = null;
		$this->hdop = null;
		$this->vdop = null;
		$this->pdop = null;
		$this->ageOfGpsData = null;
		$this->dgpsid = null;
		$this->difference = null;
		$this->distance = null;
		$this->extensions = null;
		$this->pointType = $pointType;
	}

	/**
	 * Return point type (ROUTE|TRACK|WAYPOINT)
	 * @return string
	 */
	public function getPointType(): string
	{
		return $this->pointType;
	}

	/**
	 * Serialize object to array for JSON encoding
	 * Always returns GeoJSON format
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		// GeoJSON Point format
		$properties = [
			'ele' => SerializationHelper::floatOrNull($this->elevation),
			'time' => DateTimeHelper::formatDateTime($this->time, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT),
			'magvar' => SerializationHelper::floatOrNull($this->magVar),
			'geoidheight' => SerializationHelper::floatOrNull($this->geoidHeight),
			'name' => SerializationHelper::stringOrNull($this->name),
			'cmt' => SerializationHelper::stringOrNull($this->comment),
			'desc' => SerializationHelper::stringOrNull($this->description),
			'src' => SerializationHelper::stringOrNull($this->source),
			'link' => SerializationHelper::serialize($this->links),
			'sym' => SerializationHelper::stringOrNull($this->symbol),
			'type' => SerializationHelper::stringOrNull($this->type),
			'fix' => SerializationHelper::stringOrNull($this->fix),
			'sat' => SerializationHelper::integerOrNull($this->satellitesNumber),
			'hdop' => SerializationHelper::floatOrNull($this->hdop),
			'vdop' => SerializationHelper::floatOrNull($this->vdop),
			'pdop' => SerializationHelper::floatOrNull($this->pdop),
			'ageofdgpsdata' => SerializationHelper::floatOrNull($this->ageOfGpsData),
			'dgpsid' => SerializationHelper::integerOrNull($this->dgpsid),
			'difference' => SerializationHelper::floatOrNull($this->difference),
			'distance' => SerializationHelper::floatOrNull($this->distance),
			'extensions' => SerializationHelper::serialize($this->extensions)
		];

		// Filter out null values
		$properties = array_filter($properties, function ($value) {
			return $value !== null;
		});

		return [
			'type' => 'Feature',
			'geometry' => [
				'type' => 'Point',
				'coordinates' => [
					(float) $this->longitude,
					(float) $this->latitude,
					SerializationHelper::floatOrNull($this->elevation)
				]
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
		// Implementation of GpxSerializable interface
		// This method would be called to serialize a Point to GPX XML
		// Since PointParser already handles this, this method can be empty
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation of GpxSerializable interface
		// This method would be called to deserialize GPX XML to a Point
		// Since PointParser already handles this, this method can be empty
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'lat' => (float) $this->latitude,
			'lon' => (float) $this->longitude,
			'ele' => SerializationHelper::floatOrNull($this->elevation),
			'time' => DateTimeHelper::formatDateTime($this->time, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT),
			'magvar' => SerializationHelper::floatOrNull($this->magVar),
			'geoidheight' => SerializationHelper::floatOrNull($this->geoidHeight),
			'name' => SerializationHelper::stringOrNull($this->name),
			'cmt' => SerializationHelper::stringOrNull($this->comment),
			'desc' => SerializationHelper::stringOrNull($this->description),
			'src' => SerializationHelper::stringOrNull($this->source),
			'link' => SerializationHelper::serialize($this->links),
			'sym' => SerializationHelper::stringOrNull($this->symbol),
			'type' => SerializationHelper::stringOrNull($this->type),
			'fix' => SerializationHelper::stringOrNull($this->fix),
			'sat' => SerializationHelper::integerOrNull($this->satellitesNumber),
			'hdop' => SerializationHelper::floatOrNull($this->hdop),
			'vdop' => SerializationHelper::floatOrNull($this->vdop),
			'pdop' => SerializationHelper::floatOrNull($this->pdop),
			'ageofdgpsdata' => SerializationHelper::floatOrNull($this->ageOfGpsData),
			'dgpsid' => SerializationHelper::integerOrNull($this->dgpsid),
			'difference' => SerializationHelper::floatOrNull($this->difference),
			'distance' => SerializationHelper::floatOrNull($this->distance),
			'extensions' => SerializationHelper::serialize($this->extensions)
		];
	}
}
