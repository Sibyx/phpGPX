phpGPX\Models\Stats
===============

Class Stats




* Class name: Stats
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




Properties
----------


### $distance

    public float $distance

Distance in meters (m)



* Visibility: **public**


### $averageSpeed

    public float $averageSpeed = null

Average speed in meters per second (m/s)



* Visibility: **public**


### $averagePace

    public float $averagePace = null

Average pace in seconds per kilometer (s/km)



* Visibility: **public**


### $minAltitude

    public integer $minAltitude = null

Minimal altitude in meters (m)



* Visibility: **public**


### $maxAltitude

    public integer $maxAltitude = null

Maximal altitude in meters (m)



* Visibility: **public**


### $cumulativeElevationGain

    public integer $cumulativeElevationGain = null

Cumulative elevation gain in meters (m)



* Visibility: **public**


### $startedAt

    public \DateTime $startedAt = null

Started time



* Visibility: **public**


### $finishedAt

    public \DateTime $finishedAt = null

Ending time



* Visibility: **public**


### $duration

    public integer $duration = null

Duration is seconds



* Visibility: **public**


Methods
-------


### reset

    mixed phpGPX\Models\Stats::reset()

Reset all stats



* Visibility: **public**




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)



