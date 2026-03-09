phpGPX\Models\Link
===============

Class Link according to GPX 1.1 specification.

A link to an external resource (Web page, digital photo, video clip, etc) with additional information.


* Class name: Link
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




Properties
----------


### $href

    public string $href

URL of hyperlink.



* Visibility: **public**


### $text

    public string $text

Text of hyperlink.



* Visibility: **public**


### $type

    public string $type

Mime type of content (image/jpeg)



* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\Link::__construct()

Link constructor.



* Visibility: **public**




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)



