# Quick Start

## Loading a GPX file

```php
use phpGPX\phpGPX;
use phpGPX\Analysis\Engine;

$gpx = new phpGPX(engine: Engine::default());

$file = $gpx->load('path/to/file.gpx');
```

You can also parse GPX data from a string:

```php
$gpx = new phpGPX(engine: engine::default());

$xml = file_get_contents('path/to/file.gpx');
$file = $gpx->parse($xml);
```

## Accessing data

A `GpxFile` contains three main collections:

```php
// Waypoints - individual points of interest
foreach ($file->waypoints as $waypoint) {
    echo sprintf("%s: %f, %f\n", $waypoint->name, $waypoint->latitude, $waypoint->longitude);
}

// Tracks - ordered lists of points recorded by a GPS device
// $track->stats is populated because engine was provided above
foreach ($file->tracks as $track) {
    echo $track->name . "\n";
    echo "Distance: " . round($track->stats->distance) . " m\n";

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

$gpx = new phpGPX();
$file = $gpx->load('input.gpx');

// Save as GPX XML
$file->save('output.gpx', phpGPX::XML_FORMAT);

// Save as GeoJSON
$file->save('output.geojson', phpGPX::JSON_FORMAT);
```