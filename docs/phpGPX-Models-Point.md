phpGPX\Models\Point
===============

Class Point
GPX point representation according to GPX 1.1 specification.




* Class name: Point
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)


Constants
----------


### WAYPOINT

    const WAYPOINT = 'waypoint'





### TRACKPOINT

    const TRACKPOINT = 'track'





### ROUTEPOINT

    const ROUTEPOINT = 'route'





Properties
----------


### $latitude

    public float $latitude

The latitude of the point. Decimal degrees, WGS84 datum.

Original GPX 1.1 attribute.

* Visibility: **public**


### $longitude

    public float $longitude

The longitude of the point. Decimal degrees, WGS84 datum.

Original GPX 1.1 attribute.

* Visibility: **public**


### $elevation

    public float $elevation

Elevation (in meters) of the point.

Original GPX 1.1 attribute.

* Visibility: **public**


### $time

    public \DateTime $time

Creation/modification timestamp for element. Date and time in are in Univeral Coordinated Time (UTC), not local time!
Fractional seconds are allowed for millisecond timing in tracklogs.



* Visibility: **public**


### $magVar

    public float $magVar

Magnetic variation (in degrees) at the point
Original GPX 1.1 attribute.



* Visibility: **public**


### $geoidHeight

    public float $geoidHeight

Height (in meters) of geoid (mean sea level) above WGS84 earth ellipsoid. As defined in NMEA GGA message.

Original GPX 1.1 attribute.

* Visibility: **public**


### $name

    public string $name

The GPS name of the waypoint. This field will be transferred to and from the GPS.

GPX does not place restrictions on the length of this field or the characters contained in it.
It is up to the receiving application to validate the field before sending it to the GPS.
Original GPX 1.1 attribute.

* Visibility: **public**


### $comment

    public string $comment

GPS waypoint comment. Sent to GPS as comment.

Original GPX 1.1 attribute.

* Visibility: **public**


### $description

    public string $description

A text description of the element. Holds additional information about the element intended for the user, not the GPS.

Original GPX 1.1 attribute.

* Visibility: **public**


### $source

    public string $source

Source of data. Included to give user some idea of reliability and accuracy of data. "Garmin eTrex", "USGS quad Boston North", e.g.

Original GPX 1.1 attribute.

* Visibility: **public**


### $links

    public array<mixed,\phpGPX\Models\Link> $links

Link to additional information about the waypoint.

Original GPX 1.1 attribute.

* Visibility: **public**


### $symbol

    public string $symbol

Text of GPS symbol name. For interchange with other programs, use the exact spelling of the symbol as displayed on the GPS.

If the GPS abbreviates words, spell them out.
Original GPX 1.1 attribute.

* Visibility: **public**


### $type

    public string $type

Type (classification) of the waypoint.

Original GPX 1.1 attribute.

* Visibility: **public**


### $fix

    public string $fix

Type of GPS fix. none means GPS had no fix. To signify "the fix info is unknown, leave out fixType entirely. pps = military signal used
Possible values: {'none'|'2d'|'3d'|'dgps'|'pps'}
Original GPX 1.1 attribute.



* Visibility: **public**


### $satellitesNumber

    public integer $satellitesNumber

Number of satellites used to calculate the GPX fix. Always positive value.

Original GPX 1.1 attribute.

* Visibility: **public**


### $hdop

    public float $hdop

Horizontal dilution of precision.

Original GPX 1.1 attribute.

* Visibility: **public**


### $vdop

    public float $vdop

Vertical dilution of precision.

Original GPX 1.1 attribute.

* Visibility: **public**


### $pdop

    public float $pdop

Position dilution of precision.

Original GPX 1.1 attribute

* Visibility: **public**


### $ageOfGpsData

    public integer $ageOfGpsData

Number of seconds since last DGPS update.

Original GPX 1.1 attribute.

* Visibility: **public**


### $dgpsid

    public integer $dgpsid

ID of DGPS station used in differential correction.

Original GPX 1.1 attribute.

* Visibility: **public**


### $difference

    public float $difference

Difference in in distance (in meters) between last point.

Value is created by phpGPX library.

* Visibility: **public**


### $distance

    public float $distance

Distance from collection start in meters.

Value is created by phpGPX library.

* Visibility: **public**


### $extensions

    public \phpGPX\Models\Extensions $extensions

Objects stores GPX extensions from another namespaces.



* Visibility: **public**


### $pointType

    private string $pointType

Type of the point (parent collation type (ROUTE|WAYPOINT|TRACK))



* Visibility: **private**


Methods
-------


### __construct

    mixed phpGPX\Models\Point::__construct(string $pointType)

Point constructor.



* Visibility: **public**


#### Arguments
* $pointType **string**



### getPointType

    string phpGPX\Models\Point::getPointType()

Return point type (ROUTE|TRACK|WAYPOINT)



* Visibility: **public**




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)



