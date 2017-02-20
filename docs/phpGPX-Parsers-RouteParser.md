phpGPX\Parsers\RouteParser
===============

Class RouteParser




* Class name: RouteParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    public mixed $tagName = 'rte'





* Visibility: **public**
* This property is **static**.


### $attributeMapper

    private mixed $attributeMapper = array('name' => array('name' => 'name', 'type' => 'string'), 'cmt' => array('name' => 'comment', 'type' => 'string'), 'desc' => array('name' => 'description', 'type' => 'string'), 'src' => array('name' => 'source', 'type' => 'string'), 'link' => array('name' => 'links', 'type' => 'array'), 'number' => array('name' => 'number', 'type' => 'integer'), 'type' => array('name' => 'type', 'type' => 'string'), 'extensions' => array('name' => 'extensions', 'type' => 'object'), 'rtep' => array('name' => 'points', 'type' => 'array'))





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    array<mixed,\phpGPX\Models\Route> phpGPX\Parsers\RouteParser::parse(array<mixed,\SimpleXMLElement> $nodes)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $nodes **array&lt;mixed,\SimpleXMLElement&gt;**



### toXML

    \DOMElement phpGPX\Parsers\RouteParser::toXML(\phpGPX\Models\Route $route, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $route **[phpGPX\Models\Route](phpGPX-Models-Route.md)**
* $document **DOMDocument**



### toXMLArray

    array<mixed,\DOMElement> phpGPX\Parsers\RouteParser::toXMLArray(array $routes, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $routes **array**
* $document **DOMDocument**


