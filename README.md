# phpGPX

Simple library for reading and creating [GPX files](https://en.wikipedia.org/wiki/GPS_Exchange_Format) written in PHP.

Library has stable API now and it's prepared for public testing.

## Features

 - Full support of [official specification](http://www.topografix.com/GPX/1/1/).
 - Statistics calculation.
 - Extensions.
 - JSON & XML & PHP Array output.

### Supported Extensions
 - Garmin TrackPointExtension: http://www.garmin.com/xmlschemas/TrackPointExtension/v1
 
## Stats calculation

 - Distance (m)
 - Average speed (m/s)
 - Average pace  (s/km)
 - Min / max altitude (m)
 - Start / end (DateTime object)
 - Duration (seconds)

## Examples

### Open GPX file and load basic stats
```php
$gpx = new phpGPX();
	
$file = $gpx->load('example.gpx');
	
foreach ($gpx->tracks as $track)
{
    // Statistics for whole track
    $track->stats->toArray();
    
    foreach ($track->segments as $segment)
    {
    	// Statistics for segment of track
    	$segment->stats->toArray();
    }
}
```

### Writing to file

```php

$gpx = new phpGPX();
	
$file = $gpx->load('example.gpx');

// XML
$file->save('output.gpx', phpGPX::XML_FORMAT);
	
//JSON
$file->save('output.json', phpGPX::JSON_FORMAT);
```

Currently supported output formats:

 - XML
 - JSON

## Installation

Library is still not registered composer package so the installation requires some additional effort.

1. Add new git repository `https://github.com/Sibyx/phpGPX.git` to your `composer.json` file
2. Add `Sibyx/phpGPX` as new requirement
3. So whole composer.json file should be similar to example bellow
4. Run `composer install --no-dev` (library has dev-requirements - PHPUnit)
5. You are ready to use phpGPX

```json
{
  "name": "awesome-project",
  "license": "MIT",
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/Sibyx/phpGPX.git"
    }
  ],
  "minimum-stability": "dev",
  "require": {
    "sibyx/phpgpx": "master"
  }
}
```

# TODO

 - [ ] Create full documentation
 - [ ] Write unit tests
 - [ ] More examples
 - [ ] Register as valid composer package
 
# Contributors
 
 - [Jakub Dubec](https://github.com/Sibyx) - Initial works, maintenance
 - [Lukasz Lewandowski](https://github.com/luklewluk)
  
I wrote this library as part of my job in [Backbone s.r.o.](https://www.backbone.sk/en/).

# License

This project is licensed under the terms of the MIT license.