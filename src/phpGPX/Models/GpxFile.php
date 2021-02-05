<?php
/**
 * Created            17/02/2017 17:46
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;
use phpGPX\Parsers\ExtensionParser;
use phpGPX\Parsers\MetadataParser;
use phpGPX\Parsers\PointParser;
use phpGPX\Parsers\RouteParser;
use phpGPX\Parsers\TrackParser;
use phpGPX\phpGPX;

/**
 * Class GpxFile
 * Representation of GPX file.
 * @package phpGPX\Models
 */
class GpxFile implements Summarizable
{
	/**
	 * A list of waypoints.
	 * @var Point[]
	 */
	public $waypoints;

	/**
	 * A list of routes.
	 * @var Route[]
	 */
	public $routes;

	/**
	 * A list of tracks.
	 * @var Track[]
	 */
	public $tracks;

	/**
	 * Metadata about the file.
	 * The original GPX 1.1 attribute.
	 * @var Metadata|null
	 */
	public $metadata;

	/**
	 * @var Extensions|null
	 */
	public $extensions;

	/**
	 * Creator of GPX file.
	 * @var string|null
	 */
	public $creator;

	/**
	 * GpxFile constructor.
	 */
	public function __construct()
	{
		$this->waypoints = [];
		$this->routes = [];
		$this->tracks = [];
		$this->metadata = null;
		$this->extensions = null;
		$this->creator = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return SerializationHelper::filterNotNull([
			'creator' => SerializationHelper::stringOrNull($this->creator),
			'metadata' => SerializationHelper::serialize($this->metadata),
			'waypoints' => SerializationHelper::serialize($this->waypoints),
			'routes' => SerializationHelper::serialize($this->routes),
			'tracks' => SerializationHelper::serialize($this->tracks),
			'extensions' => SerializationHelper::serialize($this->extensions)
		]);
	}

	/**
	 * Return JSON representation of GPX file with statistics.
	 * @return string
	 */
	public function toJSON()
	{
		return json_encode($this->toArray(), phpGPX::$PRETTY_PRINT ? JSON_PRETTY_PRINT : null);
	}

	/**
	 * Create XML representation of GPX file.
	 * @return \DOMDocument
	 */
	public function toXML()
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$gpx = $document->createElementNS("http://www.topografix.com/GPX/1/1", "gpx");
		$gpx->setAttribute("version", "1.1");
		$gpx->setAttribute("creator", $this->creator ? $this->creator : phpGPX::getSignature());

		ExtensionParser::$usedNamespaces = [];

		if (!empty($this->metadata)) {
			$gpx->appendChild(MetadataParser::toXML($this->metadata, $document));
		}

		foreach ($this->waypoints as $waypoint) {
			$gpx->appendChild(PointParser::toXML($waypoint, $document));
		}

		foreach ($this->routes as $route) {
			$gpx->appendChild(RouteParser::toXML($route, $document));
		}

		foreach ($this->tracks as $track) {
			$gpx->appendChild(TrackParser::toXML($track, $document));
		}

		if (!empty($this->extensions)) {
			$gpx->appendChild(ExtensionParser::toXML($this->extensions, $document));
		}

		// Namespaces
		$schemaLocationArray = [
			'http://www.topografix.com/GPX/1/1',
			'http://www.topografix.com/GPX/1/1/gpx.xsd'
		];

		foreach (ExtensionParser::$usedNamespaces as $usedNamespace) {
			$gpx->setAttributeNS(
				"http://www.w3.org/2000/xmlns/",
				sprintf("xmlns:%s", $usedNamespace['prefix']),
				$usedNamespace['namespace']
			);

			$schemaLocationArray[] = $usedNamespace['namespace'];
			$schemaLocationArray[] = $usedNamespace['xsd'];
		}

		$gpx->setAttributeNS(
			'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation',
			implode(" ", $schemaLocationArray)
		);

		$document->appendChild($gpx);

		if (phpGPX::$PRETTY_PRINT) {
			$document->formatOutput = true;
			$document->preserveWhiteSpace = true;
		}
		return $document;
	}

	/**
	 * Save data to file according to selected format.
	 * @param string $path
	 * @param string $format
	 */
	public function save($path, $format)
	{
		switch ($format) {
			case phpGPX::XML_FORMAT:
				$document = $this->toXML();
				$document->save($path);
				break;
			case phpGPX::JSON_FORMAT:
				file_put_contents($path, $this->toJSON());
				break;
			default:
				throw new \RuntimeException("Unsupported file format!");
		};
	}
}
