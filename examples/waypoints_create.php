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

require_once '../vendor/autoload.php';

$sample_data = [
	[
		'longitude' => 9.860624216140083,
		'latitude' => 54.9328621088893,
		'elevation' => 0,
		'time' => new \DateTime("+ 1 MINUTE")
	],
	[
		'latitude' => 54.83293237320851,
		'longitude' => 9.76092208681491,
		'elevation' => 10.0,
		'time' => new \DateTime("+ 2 MINUTE")
	],
	[
		'latitude' => 54.73327743521187,
		'longitude' => 9.66187816543752,
		'elevation' => 42.42,
		'time' => new \DateTime("+ 3 MINUTE")
	],
	[
		'latitude' => 54.63342326167919,
		'longitude' => 9.562439849679859,
		'elevation' => 12,
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

$wp = [];
foreach ($sample_data as $sample_point) {
	// Creating trackpoint
	$point 						= new Point(Point::WAYPOINT);
	$point->latitude 			= $sample_point['latitude'];
	$point->longitude 			= $sample_point['longitude'];
	$point->elevation 			= $sample_point['elevation'];
	$point->time 				= $sample_point['time'];

	$wp[] 			= $point;
}

$gpx_file->waypoints = $wp;

// // Add segment to segment array of track
// $track->segments[] 				= $segment;

// // Add track to file
// $gpx_file->tracks[] 			= $track;

// GPX output
$gpx_file->save('waypoint_test.gpx', \phpGPX\phpGPX::XML_FORMAT);
