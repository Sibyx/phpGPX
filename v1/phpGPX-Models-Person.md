phpGPX\Models\Person
===============

Class Person
A person or organisation




* Class name: Person
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




Properties
----------


### $name

    public string $name

Name of person or organization.

An original GPX 1.1 attribute.

* Visibility: **public**


### $email

    public \phpGPX\Models\Email $email

E-mail address.

An original GPX 1.1 attribute.

* Visibility: **public**


### $link

    public \phpGPX\Models\Link $link

Link to Web site or other external information about person.

An original GPX 1.1 attribute.

* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\Person::__construct()

Person constructor.



* Visibility: **public**




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)



