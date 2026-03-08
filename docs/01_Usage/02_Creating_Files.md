# Creating Files

You can build GPX files programmatically.

## Building a track from scratch

```php
use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use phpGPX\phpGPX;

$gpxFile = new GpxFile();

// Optional metadata
$gpxFile->metadata = new Metadata();
$gpxFile->metadata->time = new \DateTime();
$gpxFile->metadata->description = "Morning run";

// Create a track
$track = new Track();
$track->name = "Run 2024-01-15";
$track->type = "running";

// Create a segment with points
$segment = new Segment();

$points = [
    ['lat' => 48.157, 'lon' => 17.054, 'ele' => 134, 'time' => '2024-01-15T07:00:00Z'],
    ['lat' => 48.158, 'lon' => 17.055, 'ele' => 136, 'time' => '2024-01-15T07:00:30Z'],
    ['lat' => 48.160, 'lon' => 17.057, 'ele' => 140, 'time' => '2024-01-15T07:01:00Z'],
];

foreach ($points as $data) {
    $point = new Point(Point::TRACKPOINT);
    $point->latitude = $data['lat'];
    $point->longitude = $data['lon'];
    $point->elevation = $data['ele'];
    $point->time = new \DateTime($data['time']);
    $segment->points[] = $point;
}

$track->segments[] = $segment;
$gpxFile->tracks[] = $track;

// Save to file
$gpxFile->save('morning_run.gpx', phpGPX::XML_FORMAT);
```

## Calculating stats after building

Models are pure data containers — they do not calculate stats. To populate `$track->stats` and `$segment->stats` on a file you have built programmatically, call `engine` directly:

```php
use phpGPX\Analysis\Engine;

// Build the file as above, then:
$gpxFile = Engine::default()->process($gpxFile);

echo "Distance: " . round($gpxFile->tracks[0]->stats->distance) . " m\n";
```

## Building a route

```php
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
use phpGPX\Models\Route;
use phpGPX\phpGPX;

$gpxFile = new GpxFile();

$route = new Route();
$route->name = "Hiking trail";

$waypoints = [
    ['lat' => 46.571, 'lon' => 8.414, 'ele' => 2419, 'name' => 'Start'],
    ['lat' => 46.580, 'lon' => 8.420, 'ele' => 2600, 'name' => 'Summit'],
    ['lat' => 46.575, 'lon' => 8.418, 'ele' => 2450, 'name' => 'Hut'],
];

foreach ($waypoints as $data) {
    $point = new Point(Point::ROUTEPOINT);
    $point->latitude = $data['lat'];
    $point->longitude = $data['lon'];
    $point->elevation = $data['ele'];
    $point->name = $data['name'];
    $route->points[] = $point;
}

$gpxFile->routes[] = $route;
$gpxFile->save('trail.gpx', phpGPX::XML_FORMAT);
```

## Adding waypoints

```php
use phpGPX\Models\GpxFile;
use phpGPX\Models\Link;
use phpGPX\Models\Point;
use phpGPX\phpGPX;

$gpxFile = new GpxFile();

$waypoint = new Point(Point::WAYPOINT);
$waypoint->latitude = 48.8566;
$waypoint->longitude = 2.3522;
$waypoint->elevation = 35;
$waypoint->name = "Eiffel Tower";
$waypoint->description = "Famous landmark in Paris";
$waypoint->symbol = "Landmark";

$link = new Link();
$link->href = "https://www.toureiffel.paris";
$link->text = "Official website";
$waypoint->links[] = $link;

$gpxFile->waypoints[] = $waypoint;
$gpxFile->save('places.gpx', phpGPX::XML_FORMAT);
```

## Adding extensions to points

```php
use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;

$ext = new TrackPointExtension();
$ext->hr = 145.0;
$ext->aTemp = 22.0;
$ext->cad = 85.0;

$extensions = new Extensions();
$extensions->set($ext);

$point->extensions = $extensions;
```

Reading extensions back:

```php
use phpGPX\Models\Extensions\TrackPointExtension;

$ext = $point->extensions?->get(TrackPointExtension::class);
if ($ext !== null) {
    echo "Heart rate: " . $ext->hr . " bpm\n";
}
```

## Direct XML output to browser

```php
header("Content-Type: application/gpx+xml");
header("Content-Disposition: attachment; filename=track.gpx");

echo $gpxFile->toXML()->saveXML();
exit();
```