phpGPX\phpGPX
===============

Class phpGPX




* Class name: phpGPX
* Namespace: phpGPX



Constants
----------


### JSON_FORMAT

    const JSON_FORMAT = 'json'





### XML_FORMAT

    const XML_FORMAT = 'xml'





### PACKAGE_NAME

    const PACKAGE_NAME = 'phpGPX'





### VERSION

    const VERSION = '1.0'





Properties
----------


### $CALCULATE_STATS

    public boolean $CALCULATE_STATS = true

Create Stats object for each track, segment and route



* Visibility: **public**
* This property is **static**.


### $SORT_BY_TIMESTAMP

    public boolean $SORT_BY_TIMESTAMP = false

Additional sort based on timestamp in Routes & Tracks on XML read.

Disabled by default, data should be already sorted.

* Visibility: **public**
* This property is **static**.


### $DATETIME_FORMAT

    public string $DATETIME_FORMAT = 'c'

Default DateTime output format in JSON serialization.



* Visibility: **public**
* This property is **static**.


### $DATETIME_TIMEZONE_OUTPUT

    public string $DATETIME_TIMEZONE_OUTPUT = 'UTC'

Default timezone for display.

Data are always stored in UTC timezone.

* Visibility: **public**
* This property is **static**.


### $PRETTY_PRINT

    public boolean $PRETTY_PRINT = true

Pretty print.



* Visibility: **public**
* This property is **static**.


Methods
-------


### load

    \phpGPX\Models\GpxFile phpGPX\phpGPX::load($path)

Load GPX file.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $path **mixed**



### parse

    \phpGPX\Models\GpxFile phpGPX\phpGPX::parse($xml)

Parse GPX data string.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $xml **mixed**



### getSignature

    string phpGPX\phpGPX::getSignature()

Create library signature from name and version.



* Visibility: **public**
* This method is **static**.



