# Loading Files

## From file path

The simplest way to load a GPX file:

```php
use phpGPX\phpGPX;

$file = phpGPX::load('/path/to/track.gpx');
```

## From string

Parse GPX XML directly from a string, useful when receiving data from an API or database:

```php
$xml = '<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1">
    <trk><name>My Track</name><trkseg>
        <trkpt lat="46.57" lon="8.41"><ele>2419</ele></trkpt>
    </trkseg></trk>
</gpx>';

$file = phpGPX::parse($xml);
```

## What gets parsed

When loading a GPX file, phpGPX processes:

- **Metadata** - file name, description, author, copyright, time, bounds
- **Waypoints** (`<wpt>`) - individual points with coordinates, elevation, time, and all optional GPX 1.1 attributes
- **Tracks** (`<trk>`) - containing segments (`<trkseg>`) of track points (`<trkpt>`)
- **Routes** (`<rte>`) - containing route points (`<rtept>`)
- **Extensions** - Garmin TrackPointExtension (heart rate, temperature, cadence) and unsupported extensions preserved as key-value pairs

## Automatic statistics

By default, statistics are calculated automatically when loading a file. This includes distance, elevation gain/loss, duration, speed, and pace for each track, segment, and route.

To disable automatic stats calculation:

```php
phpGPX::$CALCULATE_STATS = false;

$file = phpGPX::load('track.gpx');
// $file->tracks[0]->stats will be null
```

You can recalculate stats manually at any time:

```php
$file->tracks[0]->recalculateStats();
```