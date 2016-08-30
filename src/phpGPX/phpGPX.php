<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;


use phpGPX\Model\Route;
use phpGPX\Model\Collation;
use phpGPX\Parser\TracksParser;

class phpGPX
{

	/** @var  \SimpleXMLElement */
	private $xml;

	/** @var  Collation[] */
	public $waypoints;

	/** @var  Collation[] */
	public $routes;

	/** @var  Collation[] */
	public $tracks;

	/** @var  array */
	public $metadata;

	/** @var  array */
	public static $config 			= [

	];


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