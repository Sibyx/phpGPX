# Configuration

phpGPX is configured through two mechanisms:

1. **`Config` value object** — output formatting, passed to the `phpGPX` constructor
2. **`engine` and analyzer constructors** — processing behavior (smoothing, thresholds, sorting, etc.)

Each `phpGPX` instance carries its own configuration — there is no global state.

## phpGPX constructor

```php
new phpGPX(
    config: ?Config,                        // Output formatting (default: new Config())
    engine: ?Engine,                        // Stats analyzer engine (default: null — no stats)
    extensionRegistry: ?ExtensionRegistry,  // Extension namespace→parser mappings (default: ExtensionRegistry::default())
);
```

## Config options

```php
use phpGPX\phpGPX;
use phpGPX\Config;

$gpx = new phpGPX(config: new Config(
    // Pretty print XML and JSON output (default: true)
    prettyPrint: true,
));
```

## Default configuration

All options have sensible defaults. Creating a `phpGPX` instance without arguments uses them:

```php
$gpx = new phpGPX(); // uses all defaults
```

## Config properties reference

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `prettyPrint` | bool | `true` | Indent XML and JSON output |

!!! note "Config is for output only"
    Processing behavior (stats calculation, smoothing, sorting) is controlled by `engine` and analyzer constructor arguments, not by Config.

## Engine configuration

### Using the factory (recommended)

```php
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(engine: Engine::default(
    sortByTimestamp: true,                   // Sort points by time before analysis
    ignoreZeroElevation: false,              // Treat 0m elevation as missing
    applyElevationSmoothing: true,           // Enable elevation smoothing
    elevationSmoothingThreshold: 2,          // Minimum elevation change (m)
    elevationSmoothingSpikesThreshold: 50,   // Maximum change before spike rejection
    applyDistanceSmoothing: true,            // Enable distance smoothing
    distanceSmoothingThreshold: 2,           // Minimum movement (m) to count
    speedThreshold: 0.5,                     // Movement detection threshold (m/s)
));
```

### Building manually

```php
use phpGPX\Analysis\Engine;
use phpGPX\Analysis\DistanceAnalyzer;
use phpGPX\Analysis\ElevationAnalyzer;
use phpGPX\Analysis\AltitudeAnalyzer;
use phpGPX\Analysis\TimestampAnalyzer;
use phpGPX\Analysis\BoundsAnalyzer;
use phpGPX\Analysis\MovementAnalyzer;
use phpGPX\Analysis\TrackPointExtensionAnalyzer;

$engine = (new Engine(sortByTimestamp: true))
    ->addAnalyzer(new DistanceAnalyzer(applySmoothing: true, smoothingThreshold: 3))
    ->addAnalyzer(new ElevationAnalyzer(
        applySmoothing: true,
        smoothingThreshold: 5,
        spikesThreshold: 100,
    ))
    ->addAnalyzer(new AltitudeAnalyzer(ignoreZeroElevation: true))
    ->addAnalyzer(new TimestampAnalyzer())
    ->addAnalyzer(new BoundsAnalyzer())
    ->addAnalyzer(new MovementAnalyzer(speedThreshold: 1.0))
    ->addAnalyzer(new TrackPointExtensionAnalyzer());

$gpx = new phpGPX(engine: $engine);
```

## Full example

```php
use phpGPX\phpGPX;
use phpGPX\Config;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(
    config: new Config(prettyPrint: true),
    engine: Engine::default(
        sortByTimestamp: true,
        applyElevationSmoothing: true,
        elevationSmoothingThreshold: 5,
        speedThreshold: 0.5,
    ),
);

$file = $gpx->load('track.gpx');
```

## Multiple configurations

Since configuration is per-instance, you can use different settings for different files:

```php
$smooth = new phpGPX(engine: Engine::default(
    applyElevationSmoothing: true,
    elevationSmoothingThreshold: 5,
));

$raw = new phpGPX(engine: Engine::default());

$smoothFile = $smooth->load('track.gpx');
$rawFile = $raw->load('track.gpx');
```

## Notes

- Configuration is immutable after construction — `Config` properties are set once via constructor.
- JSON output always uses ISO 8601 UTC for datetime values (GeoJSON convention).
- Stats are produced exclusively by `Engine` and its analyzers — models are pure data containers.
- Extension registry is configured per-instance. See [Extensions](05_Extensions.md) for custom extension setup.