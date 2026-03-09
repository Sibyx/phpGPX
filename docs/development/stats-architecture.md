# Stats Architecture: Single-Pass Analyzer Engine

## Overview

phpGPX 2.x uses a **single-pass analyzer engine** for computing GPS statistics.
A single `engine` walks the GPX structure once and dispatches each point to all
registered analyzers simultaneously.

## Class Diagram

```plantuml
@startuml
skinparam linetype ortho
skinparam nodesep 60
skinparam ranksep 40
skinparam backgroundColor transparent

interface PointAnalyzerInterface {
    +begin(): void
    +visit(Point, ?Point): void
    +end(Stats): void
    +aggregateTrack(Track): void
    +finalizeFile(GpxFile): void
}

abstract class AbstractPointAnalyzer {
    +aggregateTrack(Track): void
    +finalizeFile(GpxFile): void
}

class Engine {
    -analyzers: PointAnalyzerInterface[]
    -sortByTimestamp: bool
    +addAnalyzer(PointAnalyzerInterface): self
    +{static} default(...): self
    +process(GpxFile): GpxFile
    -sortPoints(GpxFile): void
    -analyzePoints(Point[], Stats): void
    -computeDerivedStats(Stats): void
}

class DistanceAnalyzer {
    -applySmoothing: bool
    -smoothingThreshold: int
}

class ElevationAnalyzer {
    -ignoreZeroElevation: bool
    -applySmoothing: bool
    -smoothingThreshold: int
    -spikesThreshold: ?int
}

class AltitudeAnalyzer {
    -ignoreZeroElevation: bool
}

class TimestampAnalyzer
class BoundsAnalyzer
class MovementAnalyzer {
    -speedThreshold: float
}
class TrackPointExtensionAnalyzer

PointAnalyzerInterface <|.. AbstractPointAnalyzer
AbstractPointAnalyzer <|-- DistanceAnalyzer
AbstractPointAnalyzer <|-- ElevationAnalyzer
AbstractPointAnalyzer <|-- AltitudeAnalyzer
AbstractPointAnalyzer <|-- TimestampAnalyzer
AbstractPointAnalyzer <|-- BoundsAnalyzer
AbstractPointAnalyzer <|-- MovementAnalyzer
AbstractPointAnalyzer <|-- TrackPointExtensionAnalyzer

Engine o-- "0..*" PointAnalyzerInterface : analyzers

note right of Engine
  Standalone class — no middleware
  layer. Used directly by phpGPX
  or called standalone via process().
  Sorting is built-in.
end note
@enduml
```

## Lifecycle Sequence

```plantuml
@startuml
skinparam backgroundColor transparent

participant "engine" as E
participant "Analyzer 1" as A1
participant "Analyzer 2" as A2
participant "Analyzer N" as AN

opt sortByTimestamp
    E -> E: sortPoints(gpxFile)
end

group For each track
    group For each segment
        E -> A1: begin()
        E -> A2: begin()
        E -> AN: begin()

        group For each point (single pass)
            E -> A1: visit(point, prev)
            E -> A2: visit(point, prev)
            E -> AN: visit(point, prev)
        end

        E -> A1: end(segmentStats)
        E -> A2: end(segmentStats)
        E -> AN: end(segmentStats)

        E -> E: computeDerivedStats(segmentStats)
    end

    E -> A1: aggregateTrack(track)
    E -> A2: aggregateTrack(track)
    E -> AN: aggregateTrack(track)
    E -> E: computeDerivedStats(trackStats)
end

group For each route
    E -> A1: begin()
    E -> A2: begin()
    E -> AN: begin()
    note right: Same visit loop as segments
    E -> A1: end(routeStats)
    E -> A2: end(routeStats)
    E -> AN: end(routeStats)
    E -> E: computeDerivedStats(routeStats)
end

E -> A1: finalizeFile(gpxFile)
E -> A2: finalizeFile(gpxFile)
E -> AN: finalizeFile(gpxFile)

@enduml
```

## How It Fits in phpGPX

```plantuml
@startuml
skinparam backgroundColor transparent

rectangle "phpGPX::parse()" as parse
rectangle "Engine::process()\n(sort + single-pass analysis)" as engine
rectangle "Return GpxFile" as ret

parse -right-> engine : GpxFile
engine -right-> ret : GpxFile

note bottom of engine
  Optional. Sorting and all analysis
  happen in one step. No middleware
  pipeline.
end note
@enduml
```

## Built-in Analyzers

| Analyzer                      | Computes                                                   | Config                                                                           |
|-------------------------------|------------------------------------------------------------|----------------------------------------------------------------------------------|
| `DistanceAnalyzer`            | Raw distance, real distance, per-point difference/distance | `applySmoothing`, `smoothingThreshold`                                           |
| `ElevationAnalyzer`           | Cumulative elevation gain/loss                             | `ignoreZeroElevation`, `applySmoothing`, `smoothingThreshold`, `spikesThreshold` |
| `AltitudeAnalyzer`            | Min/max altitude with coordinates                          | `ignoreZeroElevation`                                                            |
| `TimestampAnalyzer`           | Start/end timestamps with coordinates                      | —                                                                                |
| `BoundsAnalyzer`              | Lat/lon bounding box (segment, track, file)                | —                                                                                |
| `MovementAnalyzer`            | Moving duration, moving average speed                      | `speedThreshold`                                                                 |
| `TrackPointExtensionAnalyzer` | HR, cadence, temperature averages/max                      | —                                                                                |

**Derived stats** (computed by the engine after all analyzers finish):
- Duration = finishedAt - startedAt
- Average speed = distance / duration
- Average pace = duration / (distance / 1000)
- Moving average speed = distance / movingDuration

## Extending with Custom Analyzers

To add a new statistic, extend `AbstractPointAnalyzer`:

```php
use phpGPX\Analysis\AbstractPointAnalyzer;
use phpGPX\Models\Point;
use phpGPX\Models\Stats;
use phpGPX\Models\Track;

class MaxSpeedAnalyzer extends AbstractPointAnalyzer
{
    private float $maxSpeed = 0;

    public function begin(): void
    {
        $this->maxSpeed = 0;
    }

    public function visit(Point $current, ?Point $previous): void
    {
        if ($previous === null || $previous->time === null || $current->time === null) {
            return;
        }

        $timeDelta = abs($current->time->getTimestamp() - $previous->time->getTimestamp());
        if ($timeDelta === 0) return;

        $distance = \phpGPX\Helpers\GeoHelper::getRawDistance($previous, $current);
        $speed = $distance / $timeDelta;

        if ($speed > $this->maxSpeed) {
            $this->maxSpeed = $speed;
        }
    }

    public function end(Stats $stats): void
    {
        // Write to stats (you may need to add a custom field or use extensions)
    }

    public function aggregateTrack(Track $track): void
    {
        // Find max across segments
    }
}
```

Then register it:

```php
$engine = Engine::default();
$engine->addAnalyzer(new MaxSpeedAnalyzer());
```
