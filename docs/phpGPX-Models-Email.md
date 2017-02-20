phpGPX\Models\Email
===============

Class Email
An email address. Broken into two parts (id and domain) to help prevent email harvesting.




* Class name: Email
* Namespace: phpGPX\Models
* This class implements: [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)




Properties
----------


### $id

    public string $id

Id half of email address (jakub.dubec)



* Visibility: **public**


### $domain

    public string $domain

Domain half of email address (gmail.com)



* Visibility: **public**


Methods
-------


### __construct

    mixed phpGPX\Models\Email::__construct()

Email constructor.



* Visibility: **public**




### toArray

    array phpGPX\Models\Summarizable::toArray()

Serialize object to array



* Visibility: **public**
* This method is defined by [phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)



