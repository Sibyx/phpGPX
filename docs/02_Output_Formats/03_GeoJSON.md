# GeoJSON

phpGPX can output data in [GeoJSON](https://geojson.org/) format (RFC 7946), which is widely supported by mapping libraries like Leaflet, Mapbox, and OpenLayers.

## Saving to file

```php
$file->save('output.geojson', phpGPX::GEOJSON_FORMAT);
```

## Getting GeoJSON as string

```php
$geoJsonString = $file->toJSON(true); // true = GeoJSON format
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
    "metadata": { }
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

## Using with Leaflet

```javascript
fetch('output.geojson')
    .then(r => r.json())
    .then(data => L.geoJSON(data).addTo(map));
```