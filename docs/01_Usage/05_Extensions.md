# Extensions

GPX 1.1 supports vendor-specific extensions. phpGPX uses an **extension registry** to map XML namespace URIs to parser classes. Known extensions are parsed into typed objects; unknown ones are preserved as key-value pairs.

## Extension Registry

The `ExtensionRegistry` maps XML namespace URIs to parser classes. By default, Garmin TrackPointExtension (v1 + v2) is registered.

```php
use phpGPX\phpGPX;
use phpGPX\Parsers\ExtensionRegistry;

// Default registry (Garmin TrackPointExtension)
$gpx = new phpGPX();

// Or explicitly
$gpx = new phpGPX(extensionRegistry: ExtensionRegistry::default());
```

### Registering custom extensions

```php
use phpGPX\phpGPX;
use phpGPX\Parsers\ExtensionRegistry;

// Via constructor
$gpx = new phpGPX(
    extensionRegistry: ExtensionRegistry::default()
        ->register('http://example.com/ext/v1', MyExtensionParser::class, 'myext'),
);

// Or via method
$gpx = new phpGPX();
$gpx->registerExtension('http://example.com/ext/v1', MyExtensionParser::class, 'myext');
```

Multiple namespaces can map to the same parser (useful for v1/v2 aliasing):

```php
$registry = (new ExtensionRegistry())
    ->register('http://example.com/ext/v1', MyExtensionParser::class, 'myext')
    ->register('http://example.com/ext/v2', MyExtensionParser::class, 'myext');
```

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
use phpGPX\phpGPX;
use phpGPX\Models\Extensions\TrackPointExtension;

$gpx = new phpGPX();
$file = $gpx->load('garmin_track.gpx');

foreach ($file->tracks as $track) {
    foreach ($track->segments as $segment) {
        foreach ($segment->points as $point) {
            $ext = $point->extensions?->get(TrackPointExtension::class);
            if ($ext !== null) {
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
$extensions->set($ext);

$point->extensions = $extensions;
```

The correct XML namespaces are handled automatically during serialization.

## Unsupported extensions

Extensions that have no registered parser are preserved as key-value pairs:

```php
// Access unsupported extensions
$unsupported = $point->extensions->unsupported;
// e.g. ['MxTimeZeroSymbol' => '10', 'color' => '-16744448']
```

Unsupported extensions are preserved during round-trip (load + save) and accessible through the `unsupported` array on the `Extensions` object.

## Creating custom extensions

To add support for a new GPX extension type, implement two interfaces:

### 1. Extension model — `ExtensionInterface`

```php
use phpGPX\Models\Extensions\AbstractExtension;
use phpGPX\Models\Extensions\ExtensionInterface;

class MyExtension extends AbstractExtension implements ExtensionInterface
{
    public const NAMESPACE = 'http://example.com/ext/v1';
    public const XSD = 'http://example.com/ext/v1/schema.xsd';
    public const TAG = 'MyExtension';

    public ?float $customValue = null;

    public static function getNamespace(): string { return self::NAMESPACE; }
    public static function getSchemaLocation(): string { return self::XSD; }
    public static function getTagName(): string { return self::TAG; }

    public function jsonSerialize(): mixed
    {
        return array_filter([
            'customValue' => $this->customValue,
        ], fn($v) => $v !== null);
    }
}
```

### 2. Extension parser — `ExtensionParserInterface`

```php
use phpGPX\Models\Extensions\ExtensionInterface;
use phpGPX\Parsers\Extensions\ExtensionParserInterface;

class MyExtensionParser implements ExtensionParserInterface
{
    public static function parse(\SimpleXMLElement $node): ExtensionInterface
    {
        $ext = new MyExtension();
        $ext->customValue = isset($node->customValue) ? (float) $node->customValue : null;
        return $ext;
    }

    public static function toXML(ExtensionInterface $extension, \DOMDocument &$document, string $prefix): \DOMElement
    {
        $node = $document->createElement("$prefix:" . MyExtension::TAG);

        if ($extension->customValue !== null) {
            $node->appendChild($document->createElement("$prefix:customValue", (string) $extension->customValue));
        }

        return $node;
    }
}
```

### 3. Register it

```php
$gpx = new phpGPX();
$gpx->registerExtension(MyExtension::NAMESPACE, MyExtensionParser::class, 'myext');

$file = $gpx->load('file_with_custom_ext.gpx');
```

The third argument is the XML namespace prefix used during serialization (defaults to `ext`).
During parsing, the prefix is extracted from the source XML automatically.

## Extension statistics via Engine

The `TrackPointExtensionAnalyzer` aggregates sensor data from `TrackPointExtension` into `Stats`:

- `averageHeartRate`, `maxHeartRate`
- `averageCadence`
- `averageTemperature`

These are computed per-segment and aggregated to track level (weighted by point count). See [Statistics](03_Statistics.md) for details.

Custom extension analyzers can be built as `PointAnalyzerInterface` implementations and registered with the engine. See [Stats Architecture](../04_Development/04_Stats_Architecture.md).