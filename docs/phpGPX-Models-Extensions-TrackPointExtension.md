phpGPX\Models\Extensions\TrackPointExtension
===============

Class TrackPointExtension
TODO: https://www8.garmin.com/xmlschemas/TrackPointExtensionv1.xsd




* Class name: TrackPointExtension
* Namespace: phpGPX\Models\Extensions
* Parent class: [phpGPX\Models\Extensions\AbstractExtension](phpGPX-Models-Extensions-AbstractExtension.md)



Constants
----------


### EXTENSION_NAMESPACE

    const EXTENSION_NAMESPACE = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1'





### EXTENSION_NAMESPACE_XSD

    const EXTENSION_NAMESPACE_XSD = 'http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd'





### EXTENSION_NAME

    const EXTENSION_NAME = 'TrackPointExtension'





### EXTENSION_NAMESPACE_PREFIX

    const EXTENSION_NAMESPACE_PREFIX = 'gpxtpx'





Properties
----------


### $speed

    public float $speed





* Visibility: **public**


### $heartRate

    public float $heartRate





* Visibility: **public**


### $avgTemperature

    public float $avgTemperature





* Visibility: **public**


### $cadence

    public float $cadence





* Visibility: **public**


### $course

    public float $course





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



