phpGPX\Parsers\MetadataParser
===============

Class MetadataParser




* Class name: MetadataParser
* Namespace: phpGPX\Parsers
* This is an **abstract** class





Properties
----------


### $tagName

    private mixed $tagName = 'metadata'





* Visibility: **private**
* This property is **static**.


### $attributeMapper

    private mixed $attributeMapper = array('name' => array('name' => 'name', 'type' => 'string'), 'desc' => array('name' => 'description', 'type' => 'string'), 'author' => array('name' => 'author', 'type' => 'object'), 'copyright' => array('name' => 'copyright', 'type' => 'object'), 'link' => array('name' => 'links', 'type' => 'array'), 'time' => array('name' => 'time', 'type' => 'object'), 'keywords' => array('name' => 'keywords', 'type' => 'string'), 'bounds' => array('name' => 'bounds', 'type' => 'object'), 'extensions' => array('name' => 'extensions', 'type' => 'object'))





* Visibility: **private**
* This property is **static**.


Methods
-------


### parse

    \phpGPX\Models\Metadata phpGPX\Parsers\MetadataParser::parse(\SimpleXMLElement $node)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $node **SimpleXMLElement**



### toXML

    mixed phpGPX\Parsers\MetadataParser::toXML(\phpGPX\Models\Metadata $metadata, \DOMDocument $document)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $metadata **[phpGPX\Models\Metadata](phpGPX-Models-Metadata.md)**
* $document **DOMDocument**


