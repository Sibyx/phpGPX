# Quick Start

## Loading a GPX file

```php
use phpGPX\phpGPX;

$file = phpGPX::load('path/to/file.gpx');
```

You can also parse GPX data from a string:

```php
$xml = file_get_contents('path/to/file.gpx');
$file = phpGPX::parse($xml);
```

## Accessing data

A `GpxFile` contains three main collections:

```php
// Waypoints - individual points of interest
foreach ($file->waypoints as $waypoint) {
    echo sprintf("%s: %f, %f\n", $waypoint->name, $waypoint->latitude, $waypoint->longitude);
}

// Tracks - ordered lists of points recorded by a GPS device
foreach ($file->tracks as $track) {
    echo $track->name . "\n";
    echo "Distance: " . $track->stats->distance . " m\n";

    foreach ($track->segments as $segment) {
        foreach ($segment->points as $point) {
            echo sprintf("  %f, %f @ %s\n", $point->latitude, $point->longitude, $point->time->format('c'));
        }
    }
}

// Routes - ordered lists of waypoints representing a planned path
foreach ($file->routes as $route) {
    echo $route->name . "\n";
    foreach ($route->points as $point) {
        echo sprintf("  %f, %f\n", $point->latitude, $point->longitude);
    }
}
```

## Saving to file

```php
use phpGPX\phpGPX;

$file = phpGPX::load('input.gpx');

// Save as GPX XML
$file->save('output.gpx', phpGPX::XML_FORMAT);

// Save as JSON
$file->save('output.json', phpGPX::JSON_FORMAT);

// Save as GeoJSON
$file->save('output.geojson', phpGPX::GEOJSON_FORMAT);
```