<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;


use phpGPX\Model\Route;
use phpGPX\Model\Collection;
use phpGPX\Parser\TracksParser;

class phpGPX
{

	/** @var  \SimpleXMLElement */
	private $xml;

	/** @var  Collection[] */
	public $waypoints;

	/** @var  Collection[] */
	public $routes;

	/** @var  Collection[] */
	public $tracks;

	/** @var  array */
	public $metadata;

	public static $CALCULATE_DISTANCE = false;
	public static $CALCULATE_AVERAGE_STATS = false;
	public static $CALCULATE_MIN_MAX = false;

	public function load($path)
	{
		$this->xml = simplexml_load_file($path);

		if (isset($this->xml->trk))
		{
			$this->tracks = TracksParser::parse($this->xml->trk);
		}
	}

	public function save($path, $filename = null)
	{

	}

	public function toString()
	{

	}

	public function toJSON()
	{

	}


}