# Extensions

GPX 1.1 supports vendor-specific extensions. phpGPX parses known extensions into typed objects and preserves unknown ones.

## Garmin TrackPointExtension

The most common extension. Provides sensor data per track point.

### Available fields

| Property | Type | Description |
|----------|------|-------------|
| `aTemp` | float | Air temperature in degrees Celsius |
| `wTemp` | float | Water temperature in degrees Celsius |
| `depth` | float | Depth in meters |
| `hr` | float | Heart rate in beats per minute |
| `cad` | float | Cadence in revolutions per minute |
| `speed` | float | Speed in meters per second |
| `course` | int | Course in degrees from true north |
| `bearing` | int | Bearing in degrees from true north |

### Reading extensions

```php
$gpx = new phpGPX();
$file = $gpx->load('garmin_track.gpx');

foreach ($file->tracks as $track) {
    foreach ($track->segments as $segment) {
        foreach ($segment->points as $point) {
            if ($point->extensions && $point->extensions->trackPointExtension) {
                $ext = $point->extensions->trackPointExtension;
                echo "HR: " . $ext->hr . " bpm\n";
                echo "Temp: " . $ext->aTemp . " C\n";
            }
        }
    }
}
```

### Writing extensions

```php
use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;

$ext = new TrackPointExtension();
$ext->hr = 145.0;
$ext->aTemp = 22.0;

$extensions = new Extensions();
$extensions->trackPointExtension = $ext;

$point->extensions = $extensions;
```

The correct XML namespaces are handled automatically during serialization.

## Unsupported extensions

Extensions that phpGPX does not have a dedicated parser for are preserved as key-value pairs:

```php
// Access unsupported extensions
$unsupported = $point->extensions->unsupported;
// e.g. ['MxTimeZeroSymbol' => 10, 'color' => -16744448]
```

Unsupported extensions are preserved during round-trip (load + save) and accessible through the `unsupported` array on the `Extensions` object.