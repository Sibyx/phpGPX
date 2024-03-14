<?php
/**
 * Created            30/08/16 15:50
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

use phpGPX\phpGPX;

require_once '../vendor/autoload.php';

$origFile = dirname(__FILE__).'/waypoint_test.gpx';
$outFile = dirname(__FILE__).'/output_waypoint_test.gpx';
// $outFile2 = dirname(__FILE__).'/output_waypoint_test2.gpx';

$gpx = new phpGPX();
$file = $gpx->load($origFile);

phpGPX::$PRETTY_PRINT = true;
$file->save($outFile, phpGPX::XML_FORMAT);

$retcode = 0;
system("diff $origFile $outFile", $retcode);
// system("diff $origFile $outFile2", $retcode);

if ($retcode != 0) {
	throw new \Exception("wapoint file incorrect");
} else {
	print "wapoint test successfull\n";
}
