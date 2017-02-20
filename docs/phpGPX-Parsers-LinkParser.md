phpGPX\Parsers\LinkParser
===============






* Class name: LinkParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    private mixed $tagName = 'link'





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    array<mixed,\phpGPX\Models\Link> phpGPX\Parsers\LinkParser::parse(array<mixed,\SimpleXMLElement> $nodes)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $nodes **array&lt;mixed,\SimpleXMLElement&gt;**



### toXMLArray

    array<mixed,\DOMElement> phpGPX\Parsers\LinkParser::toXMLArray(array<mixed,\phpGPX\Models\Link> $links, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $links **array&lt;mixed,\phpGPX\Models\Link&gt;**
* $document **DOMDocument**



### toXML

    \DOMElement phpGPX\Parsers\LinkParser::toXML(\phpGPX\Models\Link $link, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $link **[phpGPX\Models\Link](phpGPX-Models-Link.md)**
* $document **DOMDocument**


