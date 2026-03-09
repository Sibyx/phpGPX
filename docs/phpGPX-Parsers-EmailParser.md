phpGPX\Parsers\EmailParser
===============

Class EmailParser




* Class name: EmailParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    private mixed $tagName = 'email'





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    \phpGPX\Models\Email phpGPX\Parsers\EmailParser::parse(\SimpleXMLElement $node)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $node **SimpleXMLElement**



### toXML

    \DOMElement phpGPX\Parsers\EmailParser::toXML(\phpGPX\Models\Email $email, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $email **[phpGPX\Models\Email](phpGPX-Models-Email.md)**
* $document **DOMDocument**


