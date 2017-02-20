phpGPX\Models\Metadata
===============

Class Metadata
Information about the GPX file, author, and copyright restrictions goes in the metadata section.

Providing rich, meaningful information about your GPX files allows others to search for and use your GPS data.


* Class name: Metadata
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




Properties
----------


### $name

    public string $name

The name of the GPX file.

Original GPX 1.1 attribute.

* Visibility: **public**


### $description

    public string $description

A description of the contents of the GPX file.

Original GPX 1.1 attribute.

* Visibility: **public**


### $author

    public \phpGPX\Models\Person $author

The person or organization who created the GPX file.

An original GPX 1.1 attribute.

* Visibility: **public**


### $copyright

    public \phpGPX\Models\Copyright $copyright

Copyright and license information governing use of the file.

Original GPX 1.1 attribute.

* Visibility: **public**


### $links

    public array<mixed,\phpGPX\Models\Link> $links

Original GPX 1.1 attribute.



* Visibility: **public**


### $time

    public \DateTime $time

Date of GPX creation



* Visibility: **public**


### $keywords

    public string $keywords

Keywords associated with the file. Search engines or databases can use this information to classify the data.



* Visibility: **public**


### $bounds

    public \phpGPX\Models\Bounds $bounds

Minimum and maximum coordinates which describe the extent of the coordinates in the file.

Original GPX 1.1 attribute.

* Visibility: **public**


### $extensions

    public \phpGPX\Models\Extensions $extensions

Extensions.



* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\Metadata::__construct()

Metadata constructor.



* Visibility: **public**




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)



