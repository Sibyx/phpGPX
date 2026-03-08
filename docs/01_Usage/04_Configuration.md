# Configuration

phpGPX is configured through the `Config` value object, passed to the `phpGPX` constructor. Each instance carries its own configuration — there is no global state.

## All options

```php
use phpGPX\phpGPX;
use phpGPX\Config;

$gpx = new phpGPX(new Config(
    // Calculate statistics automatically on load (default: true)
    calculateStats: true,

    // Sort points by timestamp when loading (default: false)
    sortByTimestamp: false,

    // Pretty print XML and JSON output (default: true)
    prettyPrint: true,

    // Ignore elevation values of 0 in stats (default: false)
    ignoreZeroElevation: false,

    // Distance smoothing (default: false)
    applyDistanceSmoothing: false,
    distanceSmoothingThreshold: 2, // meters

    // Elevation smoothing (default: false)
    applyElevationSmoothing: false,
    elevationSmoothingThreshold: 2, // meters
    elevationSmoothingSpikesThreshold: null, // meters, or null to disable
));
```

## Default configuration

All options have sensible defaults. Creating a `phpGPX` instance without arguments uses them:

```php
$gpx = new phpGPX(); // uses all defaults
```

## Multiple configurations

Since configuration is per-instance, you can use different settings for different files:

```php
$smooth = new phpGPX(new Config(
    applyElevationSmoothing: true,
    elevationSmoothingThreshold: 5,
));

$raw = new phpGPX(new Config(
    applyElevationSmoothing: false,
));

$smoothFile = $smooth->load('track.gpx');
$rawFile = $raw->load('track.gpx');
```

## Notes

- Configuration is immutable after construction — `Config` properties are set once via constructor.
- The `sortByTimestamp` option is useful for GPX files where points are out of order, but is disabled by default since most files are already sorted.
- JSON output always uses ISO 8601 UTC for datetime values (GeoJSON convention). Datetime formatting is a consumer concern.