# phpGPX

[![Code Climate](https://codeclimate.com/github/Sibyx/phpGPX/badges/gpa.svg)](https://codeclimate.com/github/Sibyx/phpGPX)
[![Latest development](https://img.shields.io/packagist/vpre/sibyx/phpgpx.svg)](https://packagist.org/packages/sibyx/phpgpx)
[![Packagist downloads](https://img.shields.io/packagist/dm/sibyx/phpgpx.svg)](https://packagist.org/packages/sibyx/phpgpx)

PHP library for reading, creating, and manipulating [GPX files](https://en.wikipedia.org/wiki/GPS_Exchange_Format).

- Full [GPX 1.1 specification](http://www.topografix.com/GPX/1/1/) support
- Single-pass stats engine with pluggable analyzers
- Extension registry (Garmin TrackPointExtension built-in)
- XML and GeoJSON (RFC 7946) output

## Installation

```
composer require sibyx/phpgpx
```

Requires PHP >= 8.1.

## Quick Start

```php
<?php
use phpGPX\phpGPX;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(engine: Engine::default());

$file = $gpx->load('example.gpx');

foreach ($file->tracks as $track) {
    echo "Distance: " . round($track->stats->distance) . " m\n";
    echo "Duration: " . gmdate("H:i:s", $track->stats->duration) . "\n";

    foreach ($track->segments as $segment) {
        echo "  Segment distance: " . round($segment->stats->distance) . " m\n";
    }
}
```

### Saving files

```php
$file->save('output.gpx', phpGPX::XML_FORMAT);
$file->save('output.json', phpGPX::JSON_FORMAT);
```

## Advanced Usage

### Configuration

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

### Custom engine

For fine-grained control, build the engine manually with only the analyzers you need:

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

### Stats reference

The engine provides the following stats through its analyzers:

| Stat                                    | Analyzer                      |
|-----------------------------------------|-------------------------------|
| Distance (m), smoothed                  | `DistanceAnalyzer`            |
| Average speed (m/s), pace (s/km)        | derived by engine             |
| Min / max altitude with coordinates     | `AltitudeAnalyzer`            |
| Elevation gain / loss (m), smoothed     | `ElevationAnalyzer`           |
| Start / end timestamps with coordinates | `TimestampAnalyzer`           |
| Duration (seconds)                      | derived by engine             |
| Coordinate bounds (min/max lat/lon)     | `BoundsAnalyzer`              |
| Moving duration, moving avg speed       | `MovementAnalyzer`            |
| Heart rate, cadence, temperature        | `TrackPointExtensionAnalyzer` |

### Custom extensions

Built-in support for Garmin [TrackPointExtension](https://www8.garmin.com/xmlschemas/TrackPointExtensionv1.xsd) (v1 + v2). Register your own via `ExtensionInterface` + `ExtensionParserInterface`:

```php
$gpx = new phpGPX();
$gpx->registerExtension('http://example.com/ext/v1', MyExtensionParser::class, 'myext');
```

### Creating a file from scratch

```php
<?php
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;

$gpx_file = new GpxFile();

$track = new Track();
$track->name = "Morning run";
$track->type = 'RUN';

$segment = new Segment();

$point = new Point(PointType::Trackpoint);
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

$gpx_file->save('output.gpx', \phpGPX\phpGPX::XML_FORMAT);
$gpx_file->save('output.json', \phpGPX\phpGPX::JSON_FORMAT);
```

## Contributing

Contributions and feedback are welcome! Please check [the issues](https://github.com/Sibyx/phpGPX/issues).

Repository branches:
- `master` — latest stable release
- `develop` — 2.x development

This library started as part of my job at [BACKBONE, s.r.o.](https://www.backbone.sk/en/). Thank you for their support!

## License

This project is licensed under the terms of the MIT license.