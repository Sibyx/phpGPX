phpGPX\Helpers\SerializationHelper
===============

Class SerializationHelper
Contains basic serialization helpers used in summary() methods.




* Class name: SerializationHelper
* Namespace: phpGPX\Helpers
* This is an **abstract** class







Methods
-------


### integerOrNull

    integer|null phpGPX\Helpers\SerializationHelper::integerOrNull($value)

Returns integer or null.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $value **mixed**



### floatOrNull

    float|null phpGPX\Helpers\SerializationHelper::floatOrNull($value)

Returns float or null.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $value **mixed**



### stringOrNull

    null|string phpGPX\Helpers\SerializationHelper::stringOrNull($value)

Returns string or null



* Visibility: **public**
* This method is **static**.


#### Arguments
* $value **mixed**



### serialize

    array|null phpGPX\Helpers\SerializationHelper::serialize(\phpGPX\Models\Summarizable|array<mixed,\phpGPX\Models\Summarizable> $object)

Recursively traverse Summarizable objects and returns their array representation according summary() method.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $object **[phpGPX\Models\Summarizable](phpGPX-Models-Summarizable.md)|array&lt;mixed,\phpGPX\Models\Summarizable&gt;**


