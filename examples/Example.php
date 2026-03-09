<?php
/**
 * Created            30/08/16 15:50
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

use phpGPX\phpGPX;
use phpGPX\Config;
use phpGPX\Analysis\Engine;

require_once '../vendor/autoload.php';

$gpx = new phpGPX(
	config: new Config(prettyPrint: true),
	engine: Engine::default(),
);

$file = $gpx->load('endomondo.gpx');

foreach ($file->tracks as $track) {
	echo "Track: " . $track->name . "\n";
	echo "Distance: " . round($track->stats->distance) . " m\n";
	echo "Duration: " . $track->stats->duration . " s\n";
	echo "Avg speed: " . round($track->stats->averageSpeed, 2) . " m/s\n";
	echo "Elevation gain: " . round($track->stats->cumulativeElevationGain, 1) . " m\n";
	echo "Elevation loss: " . round($track->stats->cumulativeElevationLoss, 1) . " m\n";
	echo "\nFull stats:\n";
	var_dump($track->stats->jsonSerialize());
}