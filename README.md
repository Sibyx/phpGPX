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

### Extension Registry

Built-in support for Garmin [TrackPointExtension](https://www8.garmin.com/xmlschemas/TrackPointExtensionv1.xsd) (v1 + v2).
Custom extensions can be registered via `ExtensionInterface` + `ExtensionParserInterface`:

```php
$gpx = new phpGPX();
$gpx->registerExtension('http://example.com/ext/v1', MyExtensionParser::class, 'myext');
```

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
use phpGPX\Models\Point;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;

$gpx_file = new GpxFile();

$track = new Track();
$track->name = "Morning run";
$track->type = 'RUN';

$segment = new Segment();

$point = new Point(Point::TRACKPOINT);
$point->latitude = 54.9328621088893;
$point->longitude = 9.860624216140083;
$point->elevation = 0;
$point->time = new \DateTime("2024-01-15T07:00:00Z");

// Add extension data
$point->extensions = new Extensions();
$ext = new TrackPointExtension();
$ext->hr = 145.0;
$point->extensions->set($ext);

$segment->points[] = $point;
$track->segments[] = $segment;
$gpx_file->tracks[] = $track;

// Save as GPX XML
$gpx_file->save('output.gpx', \phpGPX\phpGPX::XML_FORMAT);

// Save as GeoJSON
$gpx_file->save('output.json', \phpGPX\phpGPX::JSON_FORMAT);
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