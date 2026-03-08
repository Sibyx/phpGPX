# Statistics

phpGPX automatically calculates statistics for tracks, segments, and routes when loading GPX files.

## Available statistics

The `Stats` object provides:

| Property | Type | Description |
|----------|------|-------------|
| `distance` | float | Distance in meters (2D, horizontal only) |
| `realDistance` | float | Distance in meters including elevation changes (3D) |
| `averageSpeed` | float | Average speed in m/s |
| `averagePace` | float | Average pace in s/km |
| `minAltitude` | float | Minimum elevation in meters |
| `maxAltitude` | float | Maximum elevation in meters |
| `cumulativeElevationGain` | float | Total ascent in meters |
| `cumulativeElevationLoss` | float | Total descent in meters |
| `startedAt` | DateTime | Timestamp of first point |
| `finishedAt` | DateTime | Timestamp of last point |
| `duration` | float | Total duration in seconds |

Coordinate properties are also available: `startedAtCoords`, `finishedAtCoords`, `minAltitudeCoords`, `maxAltitudeCoords` — each an array with `lat` and `lng` keys.

## Accessing statistics

```php
use phpGPX\phpGPX;

$gpx = new phpGPX();
$file = $gpx->load('track.gpx');

foreach ($file->tracks as $track) {
    $stats = $track->stats;

    echo "Distance: " . round($stats->distance) . " m\n";
    echo "Real distance: " . round($stats->realDistance) . " m\n";
    echo "Elevation gain: " . round($stats->cumulativeElevationGain) . " m\n";
    echo "Duration: " . gmdate("H:i:s", $stats->duration) . "\n";
    echo "Average speed: " . round($stats->averageSpeed * 3.6, 1) . " km/h\n";

    // Per-segment stats
    foreach ($track->segments as $i => $segment) {
        echo "  Segment $i: " . round($segment->stats->distance) . " m\n";
    }
}
```

## Recalculating statistics

After modifying points, recalculate by passing a `Config` object:

```php
use phpGPX\Config;

$config = new Config();
$track->recalculateStats($config);
```

For tracks, this recalculates each segment's stats first, then aggregates them.

## Distance smoothing

GPS noise can inflate distance measurements. Enable smoothing to filter out small movements:

```php
use phpGPX\Config;

$gpx = new phpGPX(new Config(
    applyDistanceSmoothing: true,
    distanceSmoothingThreshold: 2, // meters — ignore movements smaller than this
));
```

## Elevation smoothing

GPS altitude data is often noisy. Smoothing helps get more accurate elevation gain/loss:

```php
use phpGPX\Config;

$gpx = new phpGPX(new Config(
    applyElevationSmoothing: true,
    elevationSmoothingThreshold: 2, // meters — minimum change to count

    // Optional: filter spikes (e.g. GPS glitches showing 100m jumps)
    elevationSmoothingSpikesThreshold: 50, // meters — maximum change to count
));
```

## Ignoring zero elevation

Some GPS devices record elevation as 0 when they lose satellite fix. Ignore these points:

```php
use phpGPX\Config;

$gpx = new phpGPX(new Config(ignoreZeroElevation: true));
```