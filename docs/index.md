---
title: phpGPX Documentation
---

# phpGPX

A PHP library for reading, creating, and manipulating [GPX files](https://en.wikipedia.org/wiki/GPS_Exchange_Format).

## Features

- Full support of [GPX 1.1 specification](http://www.topografix.com/GPX/1/1/)
- Statistics calculation (distance, elevation, speed, pace, duration)
- Extension support (Garmin TrackPointExtension)
- Output in XML, JSON, and GeoJSON formats

## Quick Example

```php
use phpGPX\phpGPX;

$file = phpGPX::load('track.gpx');

foreach ($file->tracks as $track) {
    echo $track->stats->distance . " meters\n";
    echo $track->stats->cumulativeElevationGain . " meters gained\n";
}
```

## Requirements

- PHP >= 8.1
- `ext-simplexml`
- `ext-dom`