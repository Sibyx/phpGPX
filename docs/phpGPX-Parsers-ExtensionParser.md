phpGPX\Parsers\ExtensionParser
===============

Class ExtensionParser




* Class name: ExtensionParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    public mixed $tagName = 'extensions'





* Visibility: **public**
* This property is **static**.


### $usedNamespaces

    public mixed $usedNamespaces = array()





* Visibility: **public**
* This property is **static**.


Methods
-------


### parse

    \phpGPX\Models\Extensions phpGPX\Parsers\ExtensionParser::parse(\SimpleXMLElement $nodes)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $nodes **SimpleXMLElement**



### toXML

    \DOMElement|null phpGPX\Parsers\ExtensionParser::toXML(\phpGPX\Models\Extensions $extensions, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $extensions **[phpGPX\Models\Extensions](phpGPX-Models-Extensions.md)**
* $document **DOMDocument**


