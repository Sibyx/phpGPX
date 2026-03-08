---
title: phpGPX Documentation
---

# phpGPX

A PHP library for reading, creating, and manipulating [GPX files](https://en.wikipedia.org/wiki/GPS_Exchange_Format).

## Features

- Full support of [GPX 1.1 specification](http://www.topografix.com/GPX/1/1/)
- Single-pass stats engine with pluggable analyzers
- Extension registry — built-in Garmin TrackPointExtension, custom extensions via `ExtensionInterface`
- GeoJSON output (RFC 7946) and GPX XML output

## Quick Example

```php
use phpGPX\phpGPX;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(engine: Engine::default());
$file = $gpx->load('track.gpx');

foreach ($file->tracks as $track) {
    echo $track->stats->distance . " meters\n";
    echo $track->stats->cumulativeElevationGain . " meters gained\n";
}
```

## Requirements

- PHP >= 8.1
- `ext-simplexml`
- `ext-dom`