phpGPX\Models\Segment
===============

Class Segment
A Track Segment holds a list of Track Points which are logically connected in order.

To represent a single GPS track where GPS reception was lost, or the GPS receiver was turned off,
start a new Track Segment for each continuous span of track data.


* Class name: Segment
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md), [phpGPX\Models\StatsCalculator](phpGPX-Models-StatsCalculator.md)




Properties
----------


### $points

    public array<mixed,\phpGPX\Models\Point> $points

Array of segment points



* Visibility: **public**


### $extensions

    public \phpGPX\Models\Extensions $extensions

You can add extend GPX by adding your own elements from another schema here.



* Visibility: **public**


### $stats

    public \phpGPX\Models\Stats $stats





* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\Segment::__construct()

Segment constructor.



* Visibility: **public**




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



