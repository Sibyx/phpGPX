---
title: phpGPX Documentation
---

# phpGPX

A PHP library for reading, creating, and manipulating [GPX files](https://en.wikipedia.org/wiki/GPS_Exchange_Format).

## Features

- Full support of [GPX 1.1 specification](http://www.topografix.com/GPX/1/1/)
- Statistics calculation (distance, elevation, speed, pace, duration)
- Extension support (Garmin TrackPointExtension)
- GeoJSON output (RFC 7946) and GPX XML output
- Instance-based API with injectable configuration

## Quick Example

```php
use phpGPX\phpGPX;

$gpx = new phpGPX();
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