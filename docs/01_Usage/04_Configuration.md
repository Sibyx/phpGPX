# Configuration

phpGPX is configured through static properties on the `phpGPX` class. Set these before loading or creating files.

## All options

```php
use phpGPX\phpGPX;

// Calculate statistics automatically on load (default: true)
phpGPX::$CALCULATE_STATS = true;

// Sort points by timestamp when loading (default: false)
phpGPX::$SORT_BY_TIMESTAMP = false;

// DateTime format for JSON output (default: 'c' — ISO 8601)
phpGPX::$DATETIME_FORMAT = 'c';

// Timezone for DateTime output (default: null — uses UTC)
phpGPX::$DATETIME_TIMEZONE_OUTPUT = 'UTC';

// Pretty print XML and JSON output (default: true)
phpGPX::$PRETTY_PRINT = true;

// Ignore elevation values of 0 in stats (default: false)
phpGPX::$IGNORE_ELEVATION_0 = false;

// Distance smoothing (default: false)
phpGPX::$APPLY_DISTANCE_SMOOTHING = false;
phpGPX::$DISTANCE_SMOOTHING_THRESHOLD = 2; // meters

// Elevation smoothing (default: false)
phpGPX::$APPLY_ELEVATION_SMOOTHING = false;
phpGPX::$ELEVATION_SMOOTHING_THRESHOLD = 2; // meters
phpGPX::$ELEVATION_SMOOTHING_SPIKES_THRESHOLD = null; // meters, or null to disable
```

## Notes

- All configuration is global via static properties. There is no per-file configuration.
- Settings affect both loading (parsing + stats calculation) and saving (serialization format).
- The `$SORT_BY_TIMESTAMP` option is useful for GPX files where points are out of order, but is disabled by default since most files are already sorted.