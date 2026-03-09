phpGPX\Models\Route
===============

Class Route




* Class name: Route
* Namespace: phpGPX\Models
* Parent class: [phpGPX\Models\Collection](phpGPX-Models-Collection.md)





Properties
----------


### $points

    public array<mixed,\phpGPX\Models\Point> $points

A list of route points.

An original GPX 1.1 attribute.

* Visibility: **public**


### $name

    public string $name

GPS name of route / track.

An original GPX 1.1 attribute.

* Visibility: **public**


### $comment

    public string $comment

GPS comment for route.

An original GPX 1.1 attribute.

* Visibility: **public**


### $description

    public string $description

Text description of route/track for user. Not sent to GPS.

An original GPX 1.1 attribute.

* Visibility: **public**


### $source

    public string $source

Source of data. Included to give user some idea of reliability and accuracy of data.

An original GPX 1.1 attribute.

* Visibility: **public**


### $links

    public array<mixed,\phpGPX\Models\Link> $links

Links to external information about the route/track.

An original GPX 1.1 attribute.

* Visibility: **public**


### $number

    public integer $number

GPS route/track number.

An original GPX 1.1 attribute.

* Visibility: **public**


### $type

    public string $type

Type (classification) of route/track.

An original GPX 1.1 attribute.

* Visibility: **public**


### $extensions

    public \phpGPX\Models\Extensions $extensions

You can add extend GPX by adding your own elements from another schema here.

An original GPX 1.1 attribute.

* Visibility: **public**


### $stats

    public \phpGPX\Models\Stats $stats

Objects contains calculated statistics for collection.



* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\Collection::__construct()

Collection constructor.



* Visibility: **public**
* This method is defined by [phpGPX\Models\Collection](phpGPX-Models-Collection.md)




### getPoints

    array<mixed,\phpGPX\Models\Point> phpGPX\Models\Collection::getPoints()

Return all points in collection.



* Visibility: **public**
* This method is **abstract**.
* This method is defined by [phpGPX\Models\Collection](phpGPX-Models-Collection.md)




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




### recalculateStats

    void phpGPX\Models\StatsCalculator::recalculateStats()

Recalculate stats objects.



* Visibility: **public**
* This method is defined by [phpGPX\Models\StatsCalculator](phpGPX-Models-StatsCalculator.md)



