# Roadmap: phpGPX 2.x

This document tracks the architectural plan and implementation phases for the phpGPX 2.0 release.
The `develop` branch is the home of all 2.x work.

## Design Principles

- **PHP 8.1+ only** — leverage enums, readonly properties, typed properties, union types
- **External parsers** — models stay clean; XML serialization lives in Parser classes (Data Mapper pattern)
- **Middleware pipeline** — replaces static config flags with composable, pluggable processing
- **GeoJSON-native JSON output** — `JsonSerializable` on models returns GeoJSON (RFC 7946)
- **Nullable properties** — GPX files with missing attributes are not rejected

## Completed

- [x] Drop PHP < 8.1 support
- [x] Upgrade to PHPUnit 10+ (supports 10.5, 11.x, 12.x)
- [x] Remove `Summarizable` interface and `toArray()` — replaced by `JsonSerializable` (#69)
- [x] Remove `GpxSerializable` interface — dead code, parsers handle XML serialization
- [x] Standardize test fixture directory naming
- [x] `PointType` enum (replaces string constants for point type mapping)

---

## Phase 1: Parser Consolidation

**Goal:** Reduce duplication across 13 parser classes by extracting shared attribute-mapping logic.

### 1.1 — Extract `AbstractParser` base class

All parsers (TrackParser, RouteParser, SegmentParser, PointParser, MetadataParser, LinkParser,
PersonParser, EmailParser, CopyrightParser, BoundsParser) share the same pattern:

```
$attributeMapper → foreach → switch (special cases) → default settype()
```

Extract into an abstract base:

```php
abstract class AbstractParser
{
    abstract protected static function getAttributeMapper(): array;
    abstract protected static function getTagName(): string;

    protected static function mapAttributes(
        \SimpleXMLElement $node,
        object $model,
        array $attributeMapper
    ): void {
        foreach ($attributeMapper as $key => $attribute) {
            if (isset($attribute['parser'])) {
                // Delegate to child parser (e.g., 'link' => LinkParser::class)
                continue;
            }
            if (!in_array($attribute['type'], ['object', 'array'])) {
                if (isset($node->$key)) {
                    $value = (string) $node->$key;
                    settype($value, $attribute['type']);
                    $model->{$attribute['name']} = $value;
                }
            }
        }
    }

    protected static function mapToXML(
        object $model,
        \DOMDocument $document,
        \DOMElement $node,
        array $attributeMapper
    ): void {
        foreach ($attributeMapper as $key => $attribute) {
            if (!is_null($model->{$attribute['name']})) {
                $child = $document->createElement($key);
                $elementText = $document->createTextNode((string) $model->{$attribute['name']});
                $child->appendChild($elementText);
                $node->appendChild($child);
            }
        }
    }
}
```

Each concrete parser overrides only the special-case handling (time, links, extensions, segments).

**Files to change:**
- `src/phpGPX/Parsers/AbstractParser.php` (new)
- All 13 existing parser classes (refactor to extend AbstractParser)

**Estimated impact:** ~40% less code in parsers, single place for type-conversion logic.

### 1.2 — Add return type declarations to all parser methods

Several parsers are missing return types (e.g., `TrackParser::parse()`, `TrackParser::toXML()`).
Add strict return types for PHP 8.4 compatibility (already partially done).

### 1.3 — Unify `$attributeMapper` format

Introduce a `'parser'` key for nested objects so special-case switches shrink:

```php
'link' => [
    'name' => 'links',
    'type' => 'array',
    'parser' => LinkParser::class,
],
'extensions' => [
    'name' => 'extensions',
    'type' => 'object',
    'parser' => ExtensionParser::class,
],
'time' => [
    'name' => 'time',
    'type' => 'datetime',  // new built-in type handled by AbstractParser
],
```

---

## Phase 2: Instance-Based `phpGPX` Entry Point

**Goal:** Replace global static configuration with an instance that carries its own settings and middleware.

### 2.1 — Convert `phpGPX` to an instance class (#68)

Current (static, global state):
```php
phpGPX::$CALCULATE_STATS = true;
phpGPX::$APPLY_ELEVATION_SMOOTHING = true;
$file = phpGPX::load('track.gpx');
```

New (instance, injectable):
```php
$gpx = new phpGPX();
$gpx->addMiddleware(new StatsMiddleware());
$gpx->addMiddleware(new ElevationSmoothingMiddleware(threshold: 2, spikesThreshold: 5));
$file = $gpx->load('track.gpx');
```

Keep a static convenience `phpGPX::load()` that creates a default-configured instance for
backward-compatible simple usage.

**Files to change:**
- `src/phpGPX/phpGPX.php` (refactor)
- `src/phpGPX/Config.php` (new — holds config as a value object instead of static properties)

### 2.2 — Move stats calculation out of parsers

Currently `TrackParser::parse()` calls `$track->recalculateStats()` inside the parse loop,
gated by `phpGPX::$CALCULATE_STATS`. This couples parsing to stats computation.

Move stats calculation into a `StatsMiddleware` that runs after parsing is complete.
Parsers should only produce the model tree from XML — nothing else.

---

## Phase 3: Middleware System (#68)

**Goal:** Composable post-parse processing pipeline.

### 3.1 — Define `MiddlewareInterface`

```php
interface MiddlewareInterface
{
    public function process(GpxFile $gpxFile): GpxFile;
}
```

### 3.2 — Implement core middlewares

| Middleware | Replaces | GitHub Issue |
|---|---|---|
| `StatsMiddleware` | `phpGPX::$CALCULATE_STATS` + `StatsCalculator` interface | — |
| `ElevationSmoothingMiddleware` | `phpGPX::$APPLY_ELEVATION_SMOOTHING` + threshold constants | #59 |
| `DistanceSmoothingMiddleware` | `phpGPX::$APPLY_DISTANCE_SMOOTHING` + threshold constant | — |
| `BoundsMiddleware` | Manual bounds — auto-compute for tracks/routes/segments | #28 |
| `TimestampSortMiddleware` | `phpGPX::$SORT_BY_TIMESTAMP` | — |
| `TrackPointExtensionStatsMiddleware` | Not yet implemented — stats from extension data (HR, cadence) | #15 |
| `MovementDurationMiddleware` | Not yet implemented — exclude pauses from duration/speed | Discussion #73 |

### 3.3 — Default middleware stack

The default `phpGPX()` instance ships with:
```php
[
    new StatsMiddleware(),
]
```

Users opt-in to everything else explicitly. This replaces the current approach where
`CALCULATE_STATS`, `APPLY_ELEVATION_SMOOTHING`, etc. are global boolean flags.

### 3.4 — Deprecate and remove static config properties

After middlewares are stable, remove the static properties from `phpGPX` class.
The `Config` value object replaces format-related settings (PRETTY_PRINT, DATETIME_FORMAT, etc.).

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

### 5.4 — Remove `StatsCalculator` interface from models

Once stats computation moves to `StatsMiddleware`, models no longer need `recalculateStats()`
or `getPoints()` on the `StatsCalculator` interface. `getPoints()` can stay as a convenience
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
- Instance-based API vs static methods
- Middleware system vs static config flags
- Extension registry vs hardcoded extensions
- `PointType` enum vs string constants

### 6.3 — Update all existing docs

Rewrite Getting Started, Usage, Configuration, Extensions sections to reflect 2.x API.

### 6.4 — Performance benchmarks (1.x vs 2.x)

Benchmark parsing and serialization of large GPX files. Ensure no regressions
from the architectural changes.

### 6.5 — Tag `2.0.0-beta.1`, then `2.0.0`

---

## Issue Tracker Cross-Reference

| Issue | Title | Phase |
|---|---|---|
| #15 | Create statistics from GPX extensions | Phase 3 (TrackPointExtensionStatsMiddleware) |
| #28 | Statistics - get Bounds of GPX Routes | Phase 3 (BoundsMiddleware) |
| #41 | Implementing waypoint and creation time extensions | Phase 4 (Extension registry) |
| #51 | startedAt/finishedAt missing timestamps | Phase 5.5 |
| #59 | Elevation gain/loss accuracy | Phase 3 (ElevationSmoothingMiddleware) |
| #68 | Middlewares | Phase 2 + 3 |
| #69 | Removal of Summarizable and toArray | Completed |
| #70 | Min altitude not necessarily first point | Completed (verify) |
| #72 | Add GPX version attribute | Phase 4.3 |
| #73 | Movement duration statistics (discussion) | Phase 3 (MovementDurationMiddleware) |
| #75 | Style extension (draft PR) | Phase 4 (Extension registry) |
| #76 | Fix documentation generation | Phase 6.1 |