phpGPX\Parsers\SegmentParser
===============

Class SegmentParser




* Class name: SegmentParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    public mixed $tagName = 'trkseg'





* Visibility: **public**
* This property is **static**.


Methods
-------


### parse

    array<mixed,\phpGPX\Models\Segment> phpGPX\Parsers\SegmentParser::parse($nodes)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $nodes **mixed** - &lt;p&gt;\SimpleXMLElement[]&lt;/p&gt;



### toXML

    \DOMElement phpGPX\Parsers\SegmentParser::toXML(\phpGPX\Models\Segment $segment, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $segment **[phpGPX\Models\Segment](phpGPX-Models-Segment.md)**
* $document **DOMDocument**



### toXMLArray

    array<mixed,\DOMElement> phpGPX\Parsers\SegmentParser::toXMLArray(array $segments, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $segments **array**
* $document **DOMDocument**


