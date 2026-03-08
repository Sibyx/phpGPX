<?php
/**
 * Created            17/02/2017 17:46
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Config;
use phpGPX\Parsers\ExtensionParser;
use phpGPX\Parsers\ExtensionRegistry;
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
class GpxFile implements \JsonSerializable
{
	/** @var Point[] */
	public array $waypoints = [];

	/** @var Route[] */
	public array $routes = [];

	/** @var Track[] */
	public array $tracks = [];

	public ?Metadata $metadata = null;

	public ?Extensions $extensions = null;

	public ?string $creator = null;

	public ?string $version = null;

	public function __construct(
		public readonly Config $config = new Config(),
	) {}

	public function jsonSerialize(): array
	{
		$features = [];

		foreach ($this->waypoints as $waypoint) {
			$features[] = $waypoint;
		}

		foreach ($this->routes as $route) {
			$features[] = $route;
		}

		foreach ($this->tracks as $track) {
			$features[] = $track;
		}

		$result = [
			'type' => 'FeatureCollection',
			'features' => $features,
		];

		if ($this->metadata !== null) {
			$result['properties'] = array_filter([
				'metadata' => $this->metadata,
				'creator' => $this->creator,
				'extensions' => $this->extensions,
			], fn($v) => $v !== null);
		}

		return $result;
	}

	/**
	 * Return GeoJSON representation of GPX file.
	 */
	public function toJSON(): string
	{
		return json_encode($this, $this->config->prettyPrint ? JSON_PRETTY_PRINT : 0);
	}

	/**
	 * Create XML representation of GPX file.
	 */
	public function toXML(): \DOMDocument
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$gpx = $document->createElementNS("http://www.topografix.com/GPX/1/1", "gpx");
		$gpx->setAttribute("version", $this->version ?? "1.1");
		$gpx->setAttribute("creator", $this->creator ? $this->creator : phpGPX::getSignature());

		ExtensionParser::$usedNamespaces = [];
		ExtensionParser::$registry ??= ExtensionRegistry::default();

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

		if ($this->extensions !== null && !$this->extensions->isEmpty()) {
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

		if ($this->config->prettyPrint) {
			$document->formatOutput = true;
			$document->preserveWhiteSpace = true;
		}
		return $document;
	}

	/**
	 * Save data to file according to selected format.
	 */
	public function save(string $path, string $format): void
	{
		switch ($format) {
			case phpGPX::XML_FORMAT:
				$document = $this->toXML();
				$document->save($path);
				break;
			case phpGPX::JSON_FORMAT:
			case phpGPX::GEOJSON_FORMAT:
				file_put_contents($path, $this->toJSON());
				break;
			default:
				throw new \RuntimeException("Unsupported file format!");
		}
	}
}