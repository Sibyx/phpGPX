<?php
/**
 * Created            30/08/16 15:50
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

use phpGPX\phpGPX;

require_once '../vendor/autoload.php';

$gpx = new phpGPX();
$gpx->load('example.gpx');

//$gpx->save('output.gpx', phpGPX::XML_FORMAT);

foreach ($gpx->tracks as $track)
{
	printf("Avg pace: %f\n", $track->stats->averagePace);
}

//$gpx->save('output.json', phpGPX::JSON_FORMAT);