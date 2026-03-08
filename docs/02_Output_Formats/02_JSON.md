# JSON (GeoJSON)

phpGPX outputs JSON in [GeoJSON](https://geojson.org/) format (RFC 7946). Both `JSON_FORMAT` and `GEOJSON_FORMAT` produce identical GeoJSON output.

## Saving to file

```php
$file->save('output.json', phpGPX::JSON_FORMAT);
// or equivalently:
$file->save('output.geojson', phpGPX::GEOJSON_FORMAT);
```

## Getting JSON as string

```php
$geoJsonString = $file->toJSON();
```

## Structure

The output is a GeoJSON `FeatureCollection`:

```json
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "geometry": {
                "type": "Point",
                "coordinates": [17.054, 48.157, 134.0]
            },
            "properties": {
                "name": "Waypoint 1",
                "ele": 134.0,
                "time": "2024-01-15T07:00:00+00:00"
            }
        },
        {
            "type": "Feature",
            "geometry": {
                "type": "MultiLineString",
                "coordinates": [
                    [[17.054, 48.157, 134.0], [17.055, 48.158, 136.0]]
                ]
            },
            "properties": {
                "name": "Morning Run",
                "stats": { "distance": 1250.5 }
            }
        }
    ],
    "properties": {
        "metadata": { "name": "My Track" },
        "creator": "phpGPX/2.0.0-alpha.2"
    }
}
```

## Geometry type mapping

| GPX element | GeoJSON geometry |
|-------------|-----------------|
| Waypoint (`<wpt>`) | `Point` |
| Route (`<rte>`) | `LineString` |
| Track (`<trk>`) | `MultiLineString` (one line per segment) |

## Coordinate order

GeoJSON uses `[longitude, latitude, elevation]` order, which is different from the GPX `lat/lon` order. phpGPX handles this conversion automatically.

## DateTime format

All datetime values are serialized as ISO 8601 UTC strings (e.g. `2024-01-15T07:00:00+00:00`). This is the industry standard for GeoJSON and data interchange formats. If you need a different format for display, transform the dates on the consumer side.

## Pretty printing

Control JSON formatting via Config:

```php
use phpGPX\Config;

$gpx = new phpGPX(new Config(prettyPrint: false));
$file = $gpx->load('track.gpx');
echo $file->toJSON(); // compact, single-line JSON
```

## Using with Leaflet

```javascript
fetch('output.geojson')
    .then(r => r.json())
    .then(data => L.geoJSON(data).addTo(map));
```