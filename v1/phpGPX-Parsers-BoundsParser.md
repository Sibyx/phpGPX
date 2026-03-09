phpGPX\Parsers\BoundsParser
===============

Class BoundsParser




* Class name: BoundsParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    private mixed $tagName = 'bounds'





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    \phpGPX\Models\Bounds|null phpGPX\Parsers\BoundsParser::parse(\SimpleXMLElement $node)

Parse data from XML.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $node **SimpleXMLElement**



### toXML

    \DOMElement phpGPX\Parsers\BoundsParser::toXML(\phpGPX\Models\Bounds $bounds, \DOMDocument $document)

Create XML representation.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $bounds **[phpGPX\Models\Bounds](phpGPX-Models-Bounds.md)**
* $document **DOMDocument**


