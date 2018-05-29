<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

use phpGPX\Models\GpxFile;
use phpGPX\Models\Link;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;

require_once '../vendor/autoload.php';

$sample_data = [
	[
		'longitude' => 9.860624216140083,
		'latitude' => 54.9328621088893,
		'elevation' => 0,
		'aTemp' => 22,
		'time' => new \DateTime("+ 1 MINUTE")
	],
	[
		'latitude' => 54.83293237320851,
		'longitude' => 9.76092208681491,
		'elevation' => 10.0,
		'aTemp' => 23,
		'time' => new \DateTime("+ 2 MINUTE")
	],
	[
		'latitude' => 54.73327743521187,
		'longitude' => 9.66187816543752,
		'elevation' => 42.42,
		'aTemp' => 24,
		'time' => new \DateTime("+ 3 MINUTE")
	],
	[
		'latitude' => 54.63342326167919,
		'longitude' => 9.562439849679859,
		'elevation' => 12,
		'aTemp' => 25,
		'time' => new \DateTime("+ 4 MINUTE")
	]
];

// Creating sample link object for metadata
$link 							= new Link();
$link->href 					= "https://sibyx.github.io/phpgpx";
$link->text 					= 'phpGPX Docs';

// GpxFile contains data and handles serialization of objects
$gpx_file 						= new GpxFile();

// Creating sample Metadata object
$gpx_file->metadata 			= new Metadata();

// Time attribute is always \DateTime object!
$gpx_file->metadata->time 		= new \DateTime();

// Description of GPX file
$gpx_file->metadata->description = "My pretty awesome GPX file, created using phpGPX library!";

// Adding link created before to links array of metadata
// Metadata of GPX file can contain more than one link
$gpx_file->metadata->links[] 	= $link;

// Creating track
$track 							= new Track();

// Name of track
$track->name 					= sprintf("Some random points in logical order. Input array should be already ordered!");

// Type of data stored in track
$track->type 					= 'RUN';

// Source of GPS coordinates
$track->source 					= sprintf("MySpecificGarminDevice");

// Creating Track segment
$segment 						= new Segment();


foreach ($sample_data as $sample_point) {
	// Creating trackpoint
	$point 						= new Point(Point::TRACKPOINT);
	$point->latitude 			= $sample_point['latitude'];
	$point->longitude 			= $sample_point['longitude'];
	$point->elevation 			= $sample_point['elevation'];
	$point->time 				= $sample_point['time'];

	// Creating trackpoint extension
	$point->extensions 			= new Extensions();
	$trackPointExtension 		= new TrackPointExtension();
	$trackPointExtension->aTemp = $sample_point['aTemp'];
	$point->extensions->trackPointExtension = $trackPointExtension;

	$segment->points[] 			= $point;
}

// Add segment to segment array of track
$track->segments[] 				= $segment;

// Add track to file
$gpx_file->tracks[] 			= $track;

// Create waypoint
$point 							= new Point(Point::WAYPOINT);
$point->name 					= 'Example Waypoint';
$point->latitude 				= $sample_point['latitude'];
$point->longitude 				= $sample_point['longitude'];
$point->elevation 				= $sample_point['elevation'];
$point->time 					= $sample_point['time'];

// Add waypoint to file
$gpx_file->waypoints[] 			= $point;

// GPX output
$gpx_file->save('CreateFileFromScratchExample.gpx', \phpGPX\phpGPX::XML_FORMAT);

// Serialized data as JSON
$gpx_file->save('CreateFileFromScratchExample.json', \phpGPX\phpGPX::JSON_FORMAT);

// Direct GPX output to browser

header("Content-Type: application/gpx+xml");
header("Content-Disposition: attachment; filename=CreatingFileFromScratchExample.gpx");

echo $gpx_file->toXML()->saveXML();
exit();
