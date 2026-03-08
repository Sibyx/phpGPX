# Roadmap: phpGPX 2.x

This document tracks the architectural plan and implementation phases for the phpGPX 2.0 release.
The `develop` branch is the home of all 2.x work.

## Design Principles

- **PHP 8.1+ only** — leverage enums, readonly properties, typed properties, union types
- **External parsers** — models stay clean; XML serialization lives in Parser classes (Data Mapper pattern)
- **Middleware pipeline** — replaces static config flags with composable, pluggable processing
- **GeoJSON-native JSON output** — `JsonSerializable` on models returns GeoJSON (RFC 7946)
- **Nullable properties** — GPX files with missing attributes are not rejected

---

## Completed

### Pre-Phase

- [x] Drop PHP < 8.1 support
- [x] Upgrade to PHPUnit 10+ (supports 10.5, 11.x, 12.x)
- [x] Remove `Summarizable` interface and `toArray()` — replaced by `JsonSerializable` (#69)
- [x] Remove `GpxSerializable` interface — dead code, parsers handle XML serialization
- [x] Standardize test fixture directory naming
- [x] `PointType` enum (replaces string constants for point type mapping)

### Phase 1: Parser Consolidation

- [x] **1.1 — AbstractParser base class**
  Extracted `AbstractParser` with four methods: `mapAttributesFromXML()`, `mapAttributesToXML()`,
  `parseDelegated()`, `serializeDelegated()`. Five parsers refactored to extend it:
  TrackParser, RouteParser, PointParser, MetadataParser, TrackPointExtensionParser.
  Remaining 8 parsers (SegmentParser, LinkParser, PersonParser, EmailParser, CopyrightParser,
  BoundsParser, ExtensionParser, WaypointParser) are standalone — they are too small or too
  specialized to benefit from the base class.

- [x] **1.2 — Return type declarations on all parser methods**
  All `parse()` and `toXML()` methods across all 13 parsers now have explicit return types.
  All `$tagName` properties typed as `string`.

- [x] **1.3 — Unified `$attributeMapper` format**
  Attribute mappers in refactored parsers use a `'parser'` key for delegated parsing and
  `'datetime'` type for DateTime fields. All switch/case blocks for delegation eliminated.
  Unified parser contract: every `parse()` accepts a single `SimpleXMLElement` node and
  returns a single model object (or null). Collection iteration is handled by
  `AbstractParser::parseDelegated()` based on `'type' => 'array'`.
  SegmentParser and LinkParser standardized to single-node contract.
  Removed all `toXMLArray` methods from refactored parsers — iteration handled by
  `serializeDelegated()`.

---

### Phase 2: Instance-Based `phpGPX` Entry Point

- [x] **2.1 — `Config` value object**
  Created `Config` class with constructor promotion. All settings are explicit, typed, documented.
  Removed `datetimeFormat` and `datetimeTimezone` — GeoJSON always outputs ISO 8601 UTC
  per industry convention (RFC 7946, Mapbox, GDAL). Datetime formatting is a consumer concern.

  Final Config properties (9):
  `calculateStats`, `sortByTimestamp`, `prettyPrint`, `ignoreZeroElevation`,
  `applyElevationSmoothing`, `elevationSmoothingThreshold`, `elevationSmoothingSpikesThreshold`,
  `applyDistanceSmoothing`, `distanceSmoothingThreshold`.

- [x] **2.2 — Instance-based `phpGPX` class (#68)**
  Rewrote `phpGPX` as an instance class. No static properties. `load()` and `parse()` are
  instance methods. Config passed via constructor: `new phpGPX(new Config(...))`.
  Only `getSignature()` and format constants remain static (stateless).
  No legacy static bridge — clean break from 1.x.

- [x] **2.3 — Stats calculation moved out of parsers**
  Removed `recalculateStats()` calls from TrackParser, RouteParser, SegmentParser.
  Parsers only produce the model tree from XML — no side effects.
  `phpGPX::parse()` handles post-processing in two steps:
  1. `sortByTimestamp` — sorts point arrays in-place via `DateTimeHelper::comparePointsByTimestamp`
  2. `calculateStats` — calls `recalculateStats(Config)` on each track and route

- [x] **2.4 — Config threaded through entire model/helper chain**
  `StatsCalculator::recalculateStats(Config $config)` — interface updated.
  `Segment`, `Route`, `Track` — `recalculateStats()` accepts Config, passes it to:
  - `ElevationGainLossCalculator::calculate(points, config)` — uses `ignoreZeroElevation`,
    `applyElevationSmoothing`, `elevationSmoothingThreshold`, `elevationSmoothingSpikesThreshold`
  - `DistanceCalculator::__construct(points, config)` — uses `applyDistanceSmoothing`,
    `distanceSmoothingThreshold`
  - Min/max altitude loops use `config->ignoreZeroElevation`
  `GpxFile` — holds Config via constructor, uses `prettyPrint` for XML/JSON output.
  `Point`, `Stats` — no Config needed. DateTime serialization uses hardcoded ISO 8601 UTC defaults.

- [x] **2.5 — All tests updated**
  Unit tests (DistanceCalculatorTest, ElevationGainLossCalculatorTest, StatsCalculationTest)
  use `new Config(...)` with named arguments instead of static property mutation.
  Integration tests (GpxFileLoadTest, XmlRoundTripTest, GeoJsonOutputTest) use `new phpGPX()`.
  86 tests, 356 assertions — all passing.

  Config property verification — every property is declared, used, and tested:
  | Property | Consumer |
  |---|---|
  | `calculateStats` | `phpGPX::parse()` |
  | `sortByTimestamp` | `phpGPX::sortPointsByTimestamp()` |
  | `prettyPrint` | `GpxFile::toJSON()`, `GpxFile::toXML()` |
  | `ignoreZeroElevation` | `ElevationGainLossCalculator`, `Segment/Route::recalculateStats()` |
  | `applyElevationSmoothing` | `ElevationGainLossCalculator` |
  | `elevationSmoothingThreshold` | `ElevationGainLossCalculator` |
  | `elevationSmoothingSpikesThreshold` | `ElevationGainLossCalculator` |
  | `applyDistanceSmoothing` | `DistanceCalculator` |
  | `distanceSmoothingThreshold` | `DistanceCalculator` |

---

## Phase 3: Middleware System (#68)

**Goal:** Composable post-parse processing pipeline for features that go beyond Config flags.

> **Note:** Phase 2 already eliminated all static config properties and replaced the core
> processing flags (stats, smoothing, sorting) with the `Config` value object. Middleware
> is for new, composable features that don't fit as simple boolean flags.

### 3.1 — Define `MiddlewareInterface`

```php
interface MiddlewareInterface
{
    public function process(GpxFile $gpxFile, Config $config): GpxFile;
}
```

### 3.2 — Implement middlewares for new features

| Middleware | Purpose | GitHub Issue |
|---|---|---|
| `BoundsMiddleware` | Auto-compute coordinate bounds for tracks/routes/segments | #28 |
| `TrackPointExtensionStatsMiddleware` | Aggregate stats from extension data (HR, cadence, power) | #15 |
| `MovementDurationMiddleware` | Exclude pauses from duration/speed calculations | Discussion #73 |

### 3.3 — Middleware pipeline in `phpGPX`

```php
$gpx = new phpGPX();
$gpx->addMiddleware(new BoundsMiddleware());
$gpx->addMiddleware(new MovementDurationMiddleware(pauseThreshold: 30));
$file = $gpx->load('track.gpx');
```

Middlewares run after parsing and after built-in Config-driven processing (sorting, stats).

---

## Phase 4: Universal Extension Processing (#41)

**Goal:** Make it easy to add new GPX extension types without modifying core code.

### 4.1 — `ExtensionInterface` contract

```php
interface ExtensionInterface extends \JsonSerializable
{
    public static function getNamespace(): string;
    public static function getNamespacePrefix(): string;
    public static function getSchemaLocation(): string;
    public static function getElementName(): string;
}
```

### 4.2 — Extension registry

Replace the hardcoded `TrackPointExtension` check in `ExtensionParser` with a registry:

```php
$gpx = new phpGPX();
$gpx->registerExtension(TrackPointExtension::class, TrackPointExtensionParser::class);
$gpx->registerExtension(StyleExtension::class, StyleExtensionParser::class); // PR #75
```

`TrackPointExtension` stays registered by default. Third-party extensions can be added
without modifying library code.

### 4.3 — Add GPX version attribute support (#72)

Parse and preserve the GPX `version` attribute on `GpxFile`.

---

## Phase 5: Strict Typing & Model Cleanup

**Goal:** Full typed codebase, clean model hierarchy.

### 5.1 — Constructor promotion for simple models

```php
// Before
class Bounds implements \JsonSerializable {
    public ?float $minLatitude;
    // ...
    public function __construct(?float $minLatitude, ...) {
        $this->minLatitude = $minLatitude;
    }
}

// After
class Bounds implements \JsonSerializable {
    public function __construct(
        public ?float $minLatitude = null,
        public ?float $minLongitude = null,
        public ?float $maxLatitude = null,
        public ?float $maxLongitude = null,
    ) {}
}
```

Apply to: Bounds, Email, Copyright, Link, Person, Extensions, TrackPointExtension.

### 5.2 — Replace `Point` string constants with `PointType` enum usage

`Point::WAYPOINT`, `Point::TRACKPOINT`, `Point::ROUTEPOINT` constants still exist alongside
the `PointType` enum. Remove the string constants, use the enum everywhere (parsers, tests).

### 5.3 — `Stats` as a value object

Consider making `Stats` immutable — constructed by `StatsMiddleware`, not mutated in-place.
The `reset()` + incremental mutation pattern is fragile. A builder or factory approach
within the middleware is cleaner.

### 5.4 — Evaluate `StatsCalculator` interface on models

Stats computation is currently driven by `phpGPX::parse()` calling `recalculateStats(Config)`
on models. Consider whether the interface should remain (allows manual re-calculation by users)
or be removed in favor of a standalone stats service. `getPoints()` can stay as a convenience
method on `Collection` without being interface-mandated.

### 5.5 — Fix startedAt/finishedAt for missing timestamps (#51)

Ensure the stats middleware correctly scans for the first/last non-null timestamp
rather than assuming boundary points have timestamps. (Partially addressed in current code,
verify with dedicated test cases.)

---

## Phase 6: Documentation & Release

### 6.1 — Fix documentation generation (#76)

Evaluate phpDocumentor vs alternatives. Set up automated doc builds in CI.

### 6.2 — Migration guide (1.x → 2.x)

Document all breaking changes:
- Removed `Summarizable` / `toArray()` — use `jsonSerialize()`
- Removed `GpxSerializable`
- Instance-based API: `new phpGPX()` instead of static `phpGPX::load()`
- `Config` value object instead of static config flags (`phpGPX::$CALCULATE_STATS`, etc.)
- `recalculateStats(Config $config)` — requires Config parameter
- JSON output is GeoJSON (RFC 7946) — no configurable datetime format (always ISO 8601 UTC)
- Middleware system for extensible post-processing
- Extension registry vs hardcoded extensions
- `PointType` enum vs string constants

### 6.3 — Update all existing docs

Rewrite Getting Started, Usage, Configuration, Extensions sections to reflect 2.x API.

### 6.4 — Performance benchmarks (1.x vs 2.x)

Benchmark parsing and serialization of large GPX files. Ensure no regressions
from the architectural changes.

---

## Issue Tracker Cross-Reference

| Issue | Title | Phase |
|---|---|---|
| #15 | Create statistics from GPX extensions | Phase 3 (TrackPointExtensionStatsMiddleware) |
| #28 | Statistics - get Bounds of GPX Routes | Phase 3 (BoundsMiddleware) |
| #41 | Implementing waypoint and creation time extensions | Phase 4 (Extension registry) |
| #51 | startedAt/finishedAt missing timestamps | Phase 5.5 |
| #59 | Elevation gain/loss accuracy | Completed (Config-driven smoothing in Phase 2) |
| #68 | Middlewares | Phase 2 (Config) + Phase 3 (pipeline) |
| #69 | Removal of Summarizable and toArray | Completed |
| #70 | Min altitude not necessarily first point | Completed (verify) |
| #72 | Add GPX version attribute | Phase 4.3 |
| #73 | Movement duration statistics (discussion) | Phase 3 (MovementDurationMiddleware) |
| #75 | Style extension (draft PR) | Phase 4 (Extension registry) |
| #76 | Fix documentation generation | Phase 6.1 |