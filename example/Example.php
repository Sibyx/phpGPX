<?php
/**
 * Created            30/08/16 15:50
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

use phpGPX\phpGPX;

require_once '../vendor/autoload.php';

$gpx = new phpGPX();
$file = $gpx->load('endomondo.gpx');

phpGPX::$PRETTY_PRINT = true;
//$file->save('output_Evening_Ride.gpx', phpGPX::XML_FORMAT);

foreach ($file->tracks as $track) {
	var_dump($track->stats->toArray());
}
