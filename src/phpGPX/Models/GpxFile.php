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
class GpxFile implements \JsonSerializable, \phpGPX\GpxSerializable
{
	/**
	 * A list of waypoints.
	 * @var Point[]
	 */
	public array $waypoints;

	/**
	 * A list of routes.
	 * @var Route[]
	 */
	public array $routes;

	/**
	 * A list of tracks.
	 * @var Track[]
	 */
	public array $tracks;

	/**
	 * Metadata about the file.
	 * The original GPX 1.1 attribute.
	 * @var Metadata|null
	 */
	public ?Metadata $metadata;

	/**
	 * @var Extensions|null
	 */
	public ?Extensions $extensions;

	/**
	 * Creator of GPX file.
	 * @var string|null
	 */
	public ?string $creator;

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
	public function toArray(): array
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
	 * Serialize object to array for JSON encoding
	 * Always returns GeoJSON format
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		// GeoJSON FeatureCollection format
		$features = [];

		// Add waypoints as Point features - each waypoint handles its own serialization
		foreach ($this->waypoints as $waypoint) {
			$features[] = $waypoint->jsonSerialize();
		}

		// Add routes as LineString features - each route handles its own serialization
		foreach ($this->routes as $route) {
			$features[] = $route->jsonSerialize();
		}

		// Add tracks as MultiLineString features - each track handles its own serialization
		foreach ($this->tracks as $track) {
			$features[] = $track->jsonSerialize();
		}

		return [
			'type' => 'FeatureCollection',
			'features' => $features,
			'metadata' => SerializationHelper::serialize($this->metadata)
		];
	}

	/**
	 * GPX serializer
	 * @param \SimpleXMLElement $node
	 * @return void
	 */
	public static function gpxSerialize(\SimpleXMLElement $node): void
	{
		// Implementation of GpxSerializable interface
		// This method would be called to serialize a GpxFile to GPX XML
		// Since the toXML method already handles this, this method can be empty
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation of GpxSerializable interface
		// This method would be called to deserialize GPX XML to a GpxFile
		// Since the parse method in phpGPX class already handles this, this method can be empty
	}


	/**
	 * Return JSON representation of GPX file with statistics.
	 * @param bool $geojson Whether to return GeoJSON format (true) or GPX format (false)
	 * @return string
	 */
	public function toJSON(bool $geojson = true): string
	{
		if ($geojson) {
			// GeoJSON format (using jsonSerialize)
			return json_encode($this->jsonSerialize(), phpGPX::$PRETTY_PRINT ? JSON_PRETTY_PRINT : null);
		} else {
			// GPX format (using toArray)
			return json_encode($this->toArray(), phpGPX::$PRETTY_PRINT ? JSON_PRETTY_PRINT : null);
		}
	}

	/**
	 * Create XML representation of GPX file.
	 * @return \DOMDocument
	 */
	public function toXML(): \DOMDocument
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
	public function save(string $path, string $format): void
	{
		switch ($format) {
			case phpGPX::XML_FORMAT:
				$document = $this->toXML();
				$document->save($path);
				break;
			case phpGPX::JSON_FORMAT:
				// Use GPX format for JSON
				file_put_contents($path, $this->toJSON(false));
				break;
			case phpGPX::GEOJSON_FORMAT:
				// Use GeoJSON format
				file_put_contents($path, $this->toJSON(true));
				break;
			default:
				throw new \RuntimeException("Unsupported file format!");
		}
	}
}
