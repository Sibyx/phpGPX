phpGPX\Parsers\PointParser
===============






* Class name: PointParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $attributeMapper

    private mixed $attributeMapper = array('ele' => array('name' => 'elevation', 'type' => 'float'), 'time' => array('name' => 'time', 'type' => 'object'), 'magvar' => array('name' => 'magVar', 'type' => 'float'), 'geoidheight' => array('name' => 'geoidHeight', 'type' => 'float'), 'name' => array('name' => 'name', 'type' => 'string'), 'cmt' => array('name' => 'comment', 'type' => 'string'), 'desc' => array('name' => 'description', 'type' => 'string'), 'src' => array('name' => 'source', 'type' => 'string'), 'link' => array('name' => 'links', 'type' => 'object'), 'sym' => array('name' => 'symbol', 'type' => 'string'), 'type' => array('name' => 'type', 'type' => 'string'), 'fix' => array('name' => 'fix', 'type' => 'string'), 'sat' => array('name' => 'satellitesNumber', 'type' => 'integer'), 'hdop' => array('name' => 'hdop', 'type' => 'float'), 'vdop' => array('name' => 'vdop', 'type' => 'float'), 'pdop' => array('name' => 'pdop', 'type' => 'float'), 'ageofdgpsdata' => array('name' => 'ageOfGpsData', 'type' => 'float'), 'dgpsid' => array('name' => 'dgpsid', 'type' => 'integer'), 'extensions' => array('name' => 'extensions', 'type' => 'object'))





* Visibility: **private**
* This property is **static**.


### $typeMapper

    private mixed $typeMapper = array('trkpt' => \phpGPX\Models\Point::TRACKPOINT, 'wpt' => \phpGPX\Models\Point::WAYPOINT, 'rtp' => \phpGPX\Models\Point::ROUTEPOINT)





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    mixed phpGPX\Parsers\PointParser::parse(\SimpleXMLElement $node)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $node **SimpleXMLElement**



### toXML

    \DOMElement phpGPX\Parsers\PointParser::toXML(\phpGPX\Models\Point $point, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $point **[phpGPX\Models\Point](phpGPX-Models-Point.md)**
* $document **DOMDocument**



### toXMLArray

    array<mixed,\DOMElement> phpGPX\Parsers\PointParser::toXMLArray(array $points, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $points **array**
* $document **DOMDocument**


