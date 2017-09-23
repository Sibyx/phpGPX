phpGPX\Models\Extensions\TrackPointExtension
===============

Class TrackPointExtension
Extension version: v2
Based on namespace: http://www.garmin.com/xmlschemas/TrackPointExtensionv2.xsd




* Class name: TrackPointExtension
* Namespace: phpGPX\Models\Extensions
* Parent class: [phpGPX\Models\Extensions\AbstractExtension](phpGPX-Models-Extensions-AbstractExtension.md)



Constants
----------


### EXTENSION_V1_NAMESPACE

    const EXTENSION_V1_NAMESPACE = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1'





### EXTENSION_V1_NAMESPACE_XSD

    const EXTENSION_V1_NAMESPACE_XSD = 'http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd'





### EXTENSION_NAMESPACE

    const EXTENSION_NAMESPACE = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v2'





### EXTENSION_NAMESPACE_XSD

    const EXTENSION_NAMESPACE_XSD = 'http://www.garmin.com/xmlschemas/TrackPointExtensionv2.xsd'





### EXTENSION_NAME

    const EXTENSION_NAME = 'TrackPointExtension'





### EXTENSION_NAMESPACE_PREFIX

    const EXTENSION_NAMESPACE_PREFIX = 'gpxtpx'





Properties
----------


### $aTemp

    public float $aTemp

Average temperature value measured in degrees Celsius.



* Visibility: **public**


### $wTemp

    public float $wTemp





* Visibility: **public**


### $depth

    public float $depth

Depth in meters.



* Visibility: **public**


### $heartRate

    public float $heartRate

Heart rate in beats per minute.



* Visibility: **public**


### $hr

    public float $hr

Heart rate in beats per minute.



* Visibility: **public**


### $cadence

    public float $cadence

Cadence in revolutions per minute.



* Visibility: **public**


### $cad

    public float $cad

Cadence in revolutions per minute.



* Visibility: **public**


### $speed

    public float $speed

Speed in meters per second.



* Visibility: **public**


### $course

    public integer $course

Course. This type contains an angle measured in degrees in a clockwise direction from the true north line.



* Visibility: **public**


### $bearing

    public integer $bearing

Bearing. This type contains an angle measured in degrees in a clockwise direction from the true north line.



* Visibility: **public**


### $namespace

    public string $namespace

XML namespace of extension



* Visibility: **public**


### $extensionName

    public string $extensionName

Node name extension.



* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\Extensions\AbstractExtension::__construct(string $namespace, string $extensionName)

AbstractExtension constructor.



* Visibility: **public**
* This method is defined by [phpGPX\Models\Extensions\AbstractExtension](phpGPX-Models-Extensions-AbstractExtension.md)


#### Arguments
* $namespace **string**
* $extensionName **string**



### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)



