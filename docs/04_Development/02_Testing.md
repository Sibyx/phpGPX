# Testing

## Running tests

```bash
# All tests
php vendor/bin/phpunit

# Unit tests only
php vendor/bin/phpunit --testsuite unit

# Integration tests only
php vendor/bin/phpunit --testsuite integration

# Single test file
php vendor/bin/phpunit tests/Unit/Models/StatsCalculationTest.php

# Single test method
php vendor/bin/phpunit --filter testSegmentStatsBasicTrack
```

## Test structure

| Directory | Purpose |
|-----------|---------|
| `tests/Unit/Helpers/` | Helper classes: GeoHelper, DateTimeHelper, SerializationHelper, DistanceCalculator, ElevationGainLossCalculator |
| `tests/Unit/Models/` | Model logic: Bounds, Stats calculation (Segment, Track, Route) |
| `tests/Unit/Parsers/` | Parser round-trip: parse XML, verify data, serialize back to XML, compare |
| `tests/Integration/` | Full pipeline: load GPX files, test serialization, XML round-trips, GeoJSON output |

## Fixture files

Test fixtures are in `tests/fixtures/` (GPX files) and `tests/Fixtures/Parsers/` (XML/JSON fragments for parser tests).

## Writing parser tests

Parser tests follow a consistent pattern. Each parser has fixtures (XML input, expected JSON output) and tests three operations:

1. **Parse** - load XML fixture, verify model properties
2. **toXML** - serialize model to XML, compare with original fixture
3. **toJSON** - serialize model to JSON, compare with expected JSON fixture