# Loading Files

## From file path

The simplest way to load a GPX file:

```php
use phpGPX\phpGPX;

$gpx = new phpGPX();
$file = $gpx->load('/path/to/track.gpx');
```

## From string

Parse GPX XML directly from a string, useful when receiving data from an API or database:

```php
$xml = '<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1">
    <trk><name>My Track</name><trkseg>
        <trkpt lat="46.57" lon="8.41"><ele>2419</ele></trkpt>
    </trkseg></trk>
</gpx>';

$gpx = new phpGPX();
$file = $gpx->parse($xml);
```

## With statistics

Statistics are not calculated by default. Pass a `engine` to populate `$track->stats`, `$segment->stats`, and `$route->stats`:

```php
use phpGPX\phpGPX;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(engine: Engine::default());

$file = $gpx->load('track.gpx');

foreach ($file->tracks as $track) {
    echo "Distance: " . round($track->stats->distance) . " m\n";
}
```

Without the engine, `$track->stats` will be `null`.

## Sorting points by timestamp

If your GPX file has out-of-order points, enable sorting on the engine:

```php
use phpGPX\phpGPX;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(engine: Engine::default(sortByTimestamp: true));

$file = $gpx->load('track.gpx');
```

## What gets parsed

When loading a GPX file, phpGPX processes:

- **Metadata** - file name, description, author, copyright, time, bounds
- **Waypoints** (`<wpt>`) - individual points with coordinates, elevation, time, and all optional GPX 1.1 attributes
- **Tracks** (`<trk>`) - containing segments (`<trkseg>`) of track points (`<trkpt>`)
- **Routes** (`<rte>`) - containing route points (`<rtept>`)
- **Extensions** - Garmin TrackPointExtension (heart rate, temperature, cadence) and unsupported extensions preserved as key-value pairs

## Processing pipeline

After parsing, the `engine` (if provided) runs a single-pass analysis over all points:

```mermaid
flowchart LR
    A[XML / string input] --> B[Parse to GpxFile]
    B --> C["engine (single pass)"]
    C --> D[Return GpxFile]
```