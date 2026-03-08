# phpGPX

[![Code Climate](https://codeclimate.com/github/Sibyx/phpGPX/badges/gpa.svg)](https://codeclimate.com/github/Sibyx/phpGPX)
[![Latest development](https://img.shields.io/packagist/vpre/sibyx/phpgpx.svg)](https://packagist.org/packages/sibyx/phpgpx)
[![Packagist downloads](https://img.shields.io/packagist/dm/sibyx/phpgpx.svg)](https://packagist.org/packages/sibyx/phpgpx)


Simple library written in PHP for reading and creating [GPX files](https://en.wikipedia.org/wiki/GPS_Exchange_Format).

Contribution and feedback is welcome! Please check the issues for TODO. I will be happy every feature or pull request.

Repository branches:

- `master`: latest stable version
- `develop`: works on `2.x`

## Features

 - Full support of [official specification](http://www.topografix.com/GPX/1/1/).
 - Single-pass stats engine with pluggable analyzers.
 - Extensions support.
 - JSON (GeoJSON) & XML output.

### Supported Extensions

- Garmin [TrackPointExtension](https://www8.garmin.com/xmlschemas/TrackPointExtensionv1.xsd):
   http://www.garmin.com/xmlschemas/TrackPointExtension/v1

### Stats calculation

Stats are provided by the `Engine` and its analyzers:

- (Smoothed) Distance (m) — `DistanceAnalyzer`
- Average speed (m/s), average pace (s/km) — derived by engine
- Min / max altitude with coordinates — `AltitudeAnalyzer`
- (Smoothed) Elevation gain / loss (m) — `ElevationAnalyzer`
- Start / end timestamps with coordinates — `TimestampAnalyzer`
- Duration (seconds) — derived by engine
- Coordinate bounds (min/max lat/lon) — `BoundsAnalyzer`
- Moving duration and moving average speed — `MovementAnalyzer`
- Heart rate, cadence, temperature — `TrackPointExtensionAnalyzer`

## Installation

You can easily install phpGPX library with [composer](https://getcomposer.org/).

```
composer require sibyx/phpgpx
```

## Examples

### Open GPX file and load basic stats

```php
<?php
use phpGPX\phpGPX;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(engine: Engine::default());

$file = $gpx->load('example.gpx');

foreach ($file->tracks as $track) {
    // Statistics for whole track
    echo "Distance: " . round($track->stats->distance) . " m\n";
    echo "Duration: " . gmdate("H:i:s", $track->stats->duration) . "\n";

    foreach ($track->segments as $segment) {
        // Statistics for segment of track
        echo "  Segment distance: " . round($segment->stats->distance) . " m\n";
    }
}
```

### Writing to file
```php
<?php
use phpGPX\phpGPX;

$gpx = new phpGPX();

$file = $gpx->load('example.gpx');

// XML
$file->save('output.gpx', phpGPX::XML_FORMAT);

// JSON (GeoJSON)
$file->save('output.json', phpGPX::JSON_FORMAT);
```

### Creating file from scratch
```php
<?php

use phpGPX\Models\GpxFile;
use phpGPX\Models\Link;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;

require_once '/vendor/autoload.php';

$sample_data = [
	[
		'longitude' => 9.860624216140083,
		'latitude' => 54.9328621088893,
		'elevation' => 0,
		'time' => new \DateTime("+ 1 MINUTE")
	],
	[
		'latitude' => 54.83293237320851,
		'longitude' => 9.76092208681491,
		'elevation' => 10.0,
		'time' => new \DateTime("+ 2 MINUTE")
	],
	[
		'latitude' => 54.73327743521187,
		'longitude' => 9.66187816543752,
		'elevation' => 42.42,
		'time' => new \DateTime("+ 3 MINUTE")
	],
	[
		'latitude' => 54.63342326167919,
		'longitude' => 9.562439849679859,
		'elevation' => 12,
		'time' => new \DateTime("+ 4 MINUTE")
	]
];

// Creating sample link object for metadata
$link = new Link();
$link->href = "https://sibyx.github.io/phpgpx";
$link->text = 'phpGPX Docs';

// GpxFile contains data and handles serialization of objects
$gpx_file = new GpxFile();

// Creating sample Metadata object
$gpx_file->metadata = new Metadata();

// Time attribute is always \DateTime object!
$gpx_file->metadata->time = new \DateTime();

// Description of GPX file
$gpx_file->metadata->description = "My pretty awesome GPX file, created using phpGPX library!";

// Adding link created before to links array of metadata
// Metadata of GPX file can contain more than one link
$gpx_file->metadata->links[] = $link;

// Creating track
$track = new Track();

// Name of track
$track->name = "Some random points in logical order. Input array should be already ordered!";

// Type of data stored in track
$track->type = 'RUN';

// Source of GPS coordinates
$track->source = "MySpecificGarminDevice";

// Creating Track segment
$segment = new Segment();

foreach ($sample_data as $sample_point) {
	// Creating trackpoint
	$point = new Point(Point::TRACKPOINT);
	$point->latitude = $sample_point['latitude'];
	$point->longitude = $sample_point['longitude'];
	$point->elevation = $sample_point['elevation'];
	$point->time = $sample_point['time'];

	$segment->points[] = $point;
}

// Add segment to segment array of track
$track->segments[] = $segment;

// Add track to file
$gpx_file->tracks[] = $track;

// GPX output
$gpx_file->save('CreatingFileFromScratchExample.gpx', \phpGPX\phpGPX::XML_FORMAT);

// Serialized data as JSON (GeoJSON)
$gpx_file->save('CreatingFileFromScratchExample.json', \phpGPX\phpGPX::JSON_FORMAT);

// Direct GPX output to browser
header("Content-Type: application/gpx+xml");
header("Content-Disposition: attachment; filename=CreatingFileFromScratchExample.gpx");

echo $gpx_file->toXML()->saveXML();
exit();
```

Currently supported output formats:

 - XML
 - JSON (GeoJSON, RFC 7946)

## Configuration

Output formatting is configured via the `Config` value object. Stats computation is configured via analyzer constructor arguments.

```php
use phpGPX\phpGPX;
use phpGPX\Config;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(
    config: new Config(prettyPrint: true),
    engine: Engine::default(
        sortByTimestamp: true,
        applyElevationSmoothing: true,
        elevationSmoothingThreshold: 2,
        ignoreZeroElevation: false,
    ),
);

$file = $gpx->load('track.gpx');
```

For fine-grained control, build the engine manually:

```php
use phpGPX\Analysis\Engine;
use phpGPX\Analysis\DistanceAnalyzer;
use phpGPX\Analysis\ElevationAnalyzer;
use phpGPX\Analysis\AltitudeAnalyzer;
use phpGPX\Analysis\TimestampAnalyzer;
use phpGPX\Analysis\BoundsAnalyzer;

$engine = (new Engine())
    ->addAnalyzer(new DistanceAnalyzer(applySmoothing: true, smoothingThreshold: 3))
    ->addAnalyzer(new ElevationAnalyzer(applySmoothing: true, spikesThreshold: 100))
    ->addAnalyzer(new AltitudeAnalyzer())
    ->addAnalyzer(new TimestampAnalyzer())
    ->addAnalyzer(new BoundsAnalyzer());

$gpx->setEngine($engine);
```

This library started as part of my job at [BACKBONE, s.r.o.](https://www.backbone.sk/en/).
Thank you very much for their support!

## License

This project is licensed under the terms of the MIT license.