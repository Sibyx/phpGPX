# Migration Guide: 1.x to 2.x

This guide covers all breaking changes when upgrading from phpGPX 1.x to 2.x.

## Requirements

- **PHP 8.1+** (was 7.1+ in early 1.x, 7.4+ in later releases)

---

## 1. Instance-Based API

The static entry point is gone. All interaction goes through an instance.

**Before (1.x):**
```php
use phpGPX\phpGPX;

phpGPX::$PRETTY_PRINT = true;
phpGPX::$CALCULATE_STATS = true;

$file = phpGPX::load('track.gpx');
```

**After (2.x):**
```php
use phpGPX\phpGPX;
use phpGPX\Config;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(
    config: new Config(prettyPrint: true),
    engine: Engine::default(),
);
$file = $gpx->load('track.gpx');
```

No engine = no stats. Pass `Engine::default()` to compute statistics on load.

---

## 2. Configuration

All static config properties are removed. `Config` is now a value object with a single output concern.

| 1.x Static Property | 2.x Equivalent |
|---|---|
| `phpGPX::$PRETTY_PRINT` | `new Config(prettyPrint: true)` |
| `phpGPX::$CALCULATE_STATS` | Pass `engine: Engine::default()` (omit for no stats) |
| `phpGPX::$APPLY_ELEVATION_SMOOTHING` | `Engine::default(applyElevationSmoothing: true)` |
| `phpGPX::$ELEVATION_SMOOTHING_THRESHOLD` | `Engine::default(elevationSmoothingThreshold: 3)` |
| `phpGPX::$ELEVATION_SMOOTHING_SPIKES_THRESHOLD` | `Engine::default(elevationSmoothingSpikesThreshold: 50)` |
| `phpGPX::$APPLY_DISTANCE_SMOOTHING` | `Engine::default(applyDistanceSmoothing: true)` |
| `phpGPX::$DISTANCE_SMOOTHING_THRESHOLD` | `Engine::default(distanceSmoothingThreshold: 5)` |
| `phpGPX::$DATETIME_FORMAT` | Removed — always ISO 8601 UTC |
| `phpGPX::$DATETIME_TIMEZONE` | Removed — always UTC |

Processing options now live on analyzer constructors, accessed via `Engine::default(...)` named arguments.

---

## 3. Stats Calculation

Models no longer compute their own statistics. The `Engine` does it in a single pass.

**Before (1.x):**
```php
$track->recalculateStats();
$segment->recalculateStats();
```

**After (2.x):**
```php
// Option A: Engine runs automatically on load
$gpx = new phpGPX(engine: Engine::default());
$file = $gpx->load('track.gpx');
// $file->tracks[0]->stats is populated

// Option B: Run engine manually
$file = (new phpGPX())->load('track.gpx');
$file = Engine::default()->process($file);
```

**Removed:**
- `recalculateStats()` on Track, Segment, Route
- `StatsCalculator` interface
- `Stats::reset()`

**New Stats fields** (all nullable):

| Field | Type | Description |
|---|---|---|
| `bounds` | `Bounds` | Coordinate bounding box |
| `movingDuration` | `float` | Duration excluding stops (seconds) |
| `movingAverageSpeed` | `float` | Speed while moving (m/s) |
| `averageHeartRate` | `float` | From TrackPointExtension (bpm) |
| `maxHeartRate` | `float` | From TrackPointExtension (bpm) |
| `averageCadence` | `float` | From TrackPointExtension (rpm) |
| `averageTemperature` | `float` | From TrackPointExtension (C) |
| `minAltitudeCoords` | `array` | Coordinate at min altitude |
| `maxAltitudeCoords` | `array` | Coordinate at max altitude |
| `startedAtCoords` | `array` | Coordinate at start time |
| `finishedAtCoords` | `array` | Coordinate at end time |

---

## 4. JSON Output is GeoJSON

JSON output now conforms to GeoJSON (RFC 7946). The structure has changed.

**Before (1.x):**
```php
$array = $track->stats->toArray();
$json = $file->toJSON();  // Custom JSON format
```

**After (2.x):**
```php
$array = $track->stats->jsonSerialize();
$json = $file->toJSON();  // GeoJSON FeatureCollection
```

**Removed:**
- `Summarizable` interface
- `toArray()` on all models

GeoJSON geometry mapping:
- Tracks → `Feature` with `MultiLineString`
- Routes → `Feature` with `LineString`
- Waypoints → `Feature` with `Point`

DateTime is always serialized as ISO 8601 UTC. There is no configurable format.

---

## 5. Extensions

The extension system is completely rewritten with a registry-based approach.

**Before (1.x):**
```php
$ext = $point->extensions->trackPointExtension;
$ext->hr; // heart rate
```

**After (2.x):**
```php
use phpGPX\Models\Extensions\TrackPointExtension;

$ext = $point->extensions?->get(TrackPointExtension::class);
$ext?->hr; // heart rate
```

The `Extensions` model is now a keyed collection:
- `set(ExtensionInterface $ext)` — store
- `get(string $class): ?ExtensionInterface` — retrieve by class
- `has(string $class): bool` — check
- `all(): array` — iterate

**Writing extensions:**
```php
use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;

$ext = new TrackPointExtension();
$ext->hr = 145.0;

$extensions = new Extensions();
$extensions->set($ext);
$point->extensions = $extensions;
```

**Custom extensions** use a registry:
```php
$gpx = new phpGPX();
$gpx->registerExtension(
    'http://example.com/ext/v1',
    MyExtensionParser::class,
    'myext',  // XML prefix for serialization
);
```

**Removed:**
- `AbstractExtension` base class — implement `ExtensionInterface` directly
- `GpxSerializable` interface
- Named extension properties on `Extensions` model

---

## 6. PointType Enum

String constants replaced by a backed enum.

**Before (1.x):**
```php
$point = new Point(Point::TRACKPOINT);
$type = $point->getPointType(); // 'trkpt'
```

**After (2.x):**
```php
use phpGPX\Models\PointType;

$point = new Point(PointType::Trackpoint);
$type = $point->getPointType();    // PointType::Trackpoint
$tag = $type->value;               // 'trkpt'
```

Enum cases: `PointType::Waypoint`, `PointType::Trackpoint`, `PointType::Routepoint`.

---

## 7. Constructor Promotion on Value Models

Simple models now use constructor promotion. Both styles work:

```php
// Named arguments (new)
$bounds = new Bounds(minLatitude: 48.0, maxLatitude: 49.0);

// Property assignment (still works)
$bounds = new Bounds();
$bounds->minLatitude = 48.0;
```

Affected models: `Bounds`, `Email`, `Copyright`, `Link`, `Person`.

---

## Quick Reference

| Removed | Replacement |
|---|---|
| `phpGPX::load()` (static) | `(new phpGPX())->load()` |
| `phpGPX::$PRETTY_PRINT` | `new Config(prettyPrint: true)` |
| `phpGPX::$CALCULATE_STATS` | `engine: Engine::default()` |
| `$model->toArray()` | `$model->jsonSerialize()` |
| `$model->recalculateStats()` | `Engine::default()->process($file)` |
| `$ext->trackPointExtension` | `$ext->get(TrackPointExtension::class)` |
| `Point::TRACKPOINT` | `PointType::Trackpoint` |
| `GpxSerializable` | Removed (parsers handle XML) |
| `Summarizable` | Removed (use `JsonSerializable`) |
| `AbstractExtension` | Implement `ExtensionInterface` |
| `StatsCalculator` | Removed (engine handles stats) |
| `Stats::reset()` | Removed (engine creates new Stats) |