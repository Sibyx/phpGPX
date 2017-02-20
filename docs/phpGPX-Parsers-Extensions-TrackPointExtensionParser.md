phpGPX\Parsers\Extensions\TrackPointExtensionParser
===============






* Class name: TrackPointExtensionParser
* Namespace: phpGPX\Parsers\Extensions





Properties
----------


### $attributeMapper

    private mixed $attributeMapper = array('atemp' => array('name' => 'avgTemperature', 'type' => 'float'), 'hr' => array('name' => 'heartRate', 'type' => 'float'), 'cad' => array('name' => 'cadence', 'type' => 'float'))





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    \phpGPX\Models\Extensions\TrackPointExtension phpGPX\Parsers\Extensions\TrackPointExtensionParser::parse(\SimpleXMLElement $node)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $node **SimpleXMLElement**



### toXML

    \DOMElement phpGPX\Parsers\Extensions\TrackPointExtensionParser::toXML(\phpGPX\Models\Extensions\TrackPointExtension $extension, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $extension **[phpGPX\Models\Extensions\TrackPointExtension](phpGPX-Models-Extensions-TrackPointExtension.md)**
* $document **DOMDocument**


