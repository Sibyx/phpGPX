phpGPX\Parsers\PersonParser
===============

Class PersonParser




* Class name: PersonParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    public mixed $tagName = 'author'





* Visibility: **public**
* This property is **static**.


Methods
-------


### parse

    \phpGPX\Models\Person phpGPX\Parsers\PersonParser::parse(\SimpleXMLElement $node)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $node **SimpleXMLElement**



### toXML

    mixed phpGPX\Parsers\PersonParser::toXML(\phpGPX\Models\Person $person, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $person **[phpGPX\Models\Person](phpGPX-Models-Person.md)**
* $document **DOMDocument**


