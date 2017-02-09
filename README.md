# phpGPX

Simple library for reading and creating [GPX files](https://en.wikipedia.org/wiki/GPS_Exchange_Format) written in PHP.

## Features

Library is still in development so not all features all implemented (see TODO). 

Currently is supported processing of Tracks in GPX files with calculation of basic stats (per collection and segment):

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
	
$gpx->load('example.gpx');
	
foreach ($gpx->tracks as $track)
{
    // Statistics for whole track
    $track->stats->summary();
    
    foreach ($track->segments as $segment)
    {
    	// Statistics for segment of track
    	$segment->stats->summary();
    }
}
```

### Writing to file

```php
// XML
$gpx->save('output.gpx', phpGPX::XML_FORMAT);
	
//JSON
$gpx->save('output.json', phpGPX::JSON_FORMAT);
```

Currently supported output formats:

 - XML
 - JSON

## Installation

Library is still not registered composer package so the installation requires some additional effort.

1. Add new git repository `https://github.com/Sibyx/phpGPX.git` to your `composer.json` file

```json
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/Sibyx/phpGPX.git"
    }
  ]
```

2. Add `Sibyx/phpGPX` as new requirement

```json
  "require": {
    "Sibyx/phpGPX": "master"
  }
```

3. So whole composer.json file should be similar to this one

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
    "Sibyx/phpGPX": "master"
  }
}
```

4. Run `composer install`

5. You are ready to use phpGPX

# TODO

 - [ ] Create full documentation
 - [ ] Write unit tests
 - [ ] More examples
 - [ ] Provide support for Waypoints & Routes
 - [ ] Register as valid composer package
 
# Contributors
 
 - [Jakub Dubec](https://github.com/Sibyx) - Initial works, maintenance
 - [luklewluk](https://github.com/luklewluk)
  
I wrote this library as part of my job in [Backbone s.r.o.](https://www.backbone.sk/en/).