<?php
/**
 * Created            30/08/16 15:50
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

use phpGPX\phpGPX;
use phpGPX\Config;

require_once '../vendor/autoload.php';

$origFile = dirname(__FILE__).'/waypoint_test.gpx';
$outFile = dirname(__FILE__).'/output_waypoint_test.gpx';

$gpx = new phpGPX(config: new Config(prettyPrint: true));
$file = $gpx->load($origFile);

$file->save($outFile, phpGPX::XML_FORMAT);

$retcode = 0;
system("diff $origFile $outFile", $retcode);

if ($retcode != 0) {
	throw new \Exception("waypoint file incorrect");
} else {
	print "waypoint test successful\n";
}