<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;


use phpGPX\Model\Route;
use phpGPX\Model\Track;
use phpGPX\Parser\TracksParser;

class phpGPX
{

	/** @var  \DOMDocument */
	private $xml;

	/** @var  Route[] */
	private $routes;

	/** @var  Track[] */
	private $tracks;

	/** @var  array */
	private $metadata;

	/** @var  array */
	public static $config 			= [

	];


	public function load($path)
	{
		$this->xml = new \DOMDocument();
		$this->xml->load($path);

		$gpx = $this->xml->getElementsByTagName('gpx');

		if (!$gpx->length)
			throw new \ErrorException("Invalid GPX file!");

		$gpx = $gpx->item(0);

		// Load tracks from file
		while ($track = TracksParser::parse($this->xml))
		{
			$this->addTrack($track);
		}


	}

	public function save($path, $filename = null)
	{

	}

	public function addRoute(Route $route)
	{
		$this->routes[$route->getName()] = $route;
	}

	public function deleteRoute(Route $route)
	{
		if (array_key_exists($route->getName(), $this->routes))
		{
			unset($this->routes[$route->getName()]);
		}
	}

	public function addTrack(Track $track)
	{
		$this->tracks[$track->getName()] = $track;
	}

	public function deleteTrack(Track $track)
	{
		if (array_key_exists($track->getName(), $this->tracks))
		{
			unset($this->tracks[$track->getName()]);
		}
	}

	public function toString()
	{

	}

	public function toJSON()
	{

	}


}