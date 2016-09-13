<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;


use phpGPX\Helpers\Utils;
use phpGPX\Models\Collection;
use phpGPX\Parsers\TrackParser;
use phpGPX\Serializers\TrackXmlSerializer;

class phpGPX
{
	const JSON_FORMAT = 'json';
	const XML_FORMAT = 'xml';

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
	public static $SORT_BY_TIMESTAMP = true;
	public static $DATETIME_FORMAT = 'c';
	public static $DATETIME_TIMEZONE_OUTPUT = 'UTC';
	public static $CREATOR = 'phpGPX';
	public static $PRETTY_PRINT = true;

	public function load($path)
	{
		$this->xml = simplexml_load_file($path);

		// Parse tracks
		if (isset($this->xml->trk))
		{
			$this->tracks = TrackParser::parse($this->xml->trk);
		}

		//TODO: parse waypoints
		//TODO: parse routes
	}

	public function save($path, $format)
	{
		switch ($format)
		{
			case self::XML_FORMAT:
				$document = $this->toXML();
				$document->save($path);
				break;
			case self::JSON_FORMAT:
				file_put_contents($path, $this->toJSON());
				break;
			default:
				throw new \RuntimeException("Unsupported file format!");
		};
	}

	public function toString()
	{
		$data = [];
	}

	public function toXML()
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$gpx = $document->createElementNS("http://www.topografix.com/GPX/1/1", "gpx");
		$gpx->setAttribute("version", "1.1");
		$gpx->setAttribute("creator", self::$CREATOR);

		// Namespaces
		$gpx->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:gpxtpx", "http://www.garmin.com/xmlschemas/TrackPointExtension/v1");
		$gpx->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:gpxx", "http://www.garmin.com/xmlschemas/GpxExtensions/v3");

		$gpx->setAttributeNS(
			'http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', implode(" ",[
				'http://www.topografix.com/GPX/1/1',
				'http://www.topografix.com/GPX/1/1/gpx.xsd',
				'http://www.garmin.com/xmlschemas/GpxExtensions/v3',
				'http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd',
				'http://www.garmin.com/xmlschemas/TrackPointExtension/v1',
				'http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd'
			])
		);

		foreach ($this->tracks as $track)
		{
			$gpx->appendChild(TrackXmlSerializer::serializeCollection($track, $document));
		}

		$document->appendChild($gpx);

		if (self::$PRETTY_PRINT)
		{
			$document->formatOutput = true;
			$document->preserveWhiteSpace = true;
		}
		return $document;
	}

	// https://soyuka.me/streaming-big-json-files-the-good-way/
	public function toJSON()
	{
		return json_encode($this->toArray(), self::$PRETTY_PRINT ? JSON_PRETTY_PRINT : null);
	}

	public function toArray()
	{
		return [
			'tracks' => Utils::serialize($this->tracks),
			'waypoints' => Utils::serialize($this->waypoints),
			'routes' => Utils::serialize($this->routes)
		];
	}


}