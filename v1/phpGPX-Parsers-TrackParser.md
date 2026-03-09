phpGPX\Parsers\TrackParser
===============

Class TrackParser




* Class name: TrackParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    public mixed $tagName = 'trk'





* Visibility: **public**
* This property is **static**.


### $attributeMapper

    private mixed $attributeMapper = array('name' => array('name' => 'name', 'type' => 'string'), 'cmt' => array('name' => 'comment', 'type' => 'string'), 'desc' => array('name' => 'description', 'type' => 'string'), 'src' => array('name' => 'source', 'type' => 'string'), 'link' => array('name' => 'links', 'type' => 'array'), 'number' => array('name' => 'number', 'type' => 'integer'), 'type' => array('name' => 'type', 'type' => 'string'), 'extensions' => array('name' => 'extensions', 'type' => 'object'), 'trkseg' => array('name' => 'segments', 'type' => 'array'))





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    array<mixed,\phpGPX\Models\Track> phpGPX\Parsers\TrackParser::parse(\SimpleXMLElement $nodes)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $nodes **SimpleXMLElement**



### toXML

    \DOMElement phpGPX\Parsers\TrackParser::toXML(\phpGPX\Models\Track $track, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $track **[phpGPX\Models\Track](phpGPX-Models-Track.md)**
* $document **DOMDocument**



### toXMLArray

    array<mixed,\DOMElement> phpGPX\Parsers\TrackParser::toXMLArray(array $tracks, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $tracks **array**
* $document **DOMDocument**


