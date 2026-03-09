phpGPX\Parsers\CopyrightParser
===============

Class CopyrightParser




* Class name: CopyrightParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    public mixed $tagName = 'copyright'





* Visibility: **public**
* This property is **static**.


Methods
-------


### parse

    \phpGPX\Models\Copyright|null phpGPX\Parsers\CopyrightParser::parse(\SimpleXMLElement $node)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $node **SimpleXMLElement**



### toXML

    \DOMElement phpGPX\Parsers\CopyrightParser::toXML(\phpGPX\Models\Copyright $copyright, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $copyright **[phpGPX\Models\Copyright](phpGPX-Models-Copyright.md)**
* $document **DOMDocument**


