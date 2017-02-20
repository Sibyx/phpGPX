phpGPX\Models\GpxFile
===============

Class GpxFile
Representation of GPX file.




* Class name: GpxFile
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




Properties
----------


### $waypoints

    public array<mixed,\phpGPX\Models\Point> $waypoints

A list of waypoints.



* Visibility: **public**


### $routes

    public array<mixed,\phpGPX\Models\Route> $routes

A list of routes.



* Visibility: **public**


### $tracks

    public array<mixed,\phpGPX\Models\Track> $tracks

A list of tracks.



* Visibility: **public**


### $metadata

    public \phpGPX\Models\Metadata $metadata

Metadata about the file.

The original GPX 1.1 attribute.

* Visibility: **public**


### $extensions

    public \phpGPX\Models\Extensions $extensions





* Visibility: **public**


### $creator

    public string $creator

Creator of GPX file.



* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\GpxFile::__construct()

GpxFile constructor.



* Visibility: **public**




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




### toJSON

    string phpGPX\Models\GpxFile::toJSON()

Return JSON representation of GPX file with statistics.



* Visibility: **public**




### toXML

    \DOMDocument phpGPX\Models\GpxFile::toXML()

Create XML representation of GPX file.



* Visibility: **public**




### save

    mixed phpGPX\Models\GpxFile::save(string $path, string $format)

Save data to file according to selected format.



* Visibility: **public**


#### Arguments
* $path **string**
* $format **string**


