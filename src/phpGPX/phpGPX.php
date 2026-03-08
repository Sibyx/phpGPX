<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;

use phpGPX\Analysis\Engine;
use phpGPX\Models\GpxFile;
use phpGPX\Parsers\ExtensionParser;
use phpGPX\Parsers\ExtensionRegistry;
use phpGPX\Parsers\MetadataParser;
use phpGPX\Parsers\RouteParser;
use phpGPX\Parsers\TrackParser;
use phpGPX\Parsers\WaypointParser;

/**
 * Class phpGPX
 * @package phpGPX
 */
class phpGPX
{
	const JSON_FORMAT = 'json';
	const XML_FORMAT = 'xml';
	const GEOJSON_FORMAT = 'geojson';

	const PACKAGE_NAME = 'phpGPX';
	const VERSION = '2.0.0-alpha.3';

	public readonly Config $config;

	private ?Engine $engine = null;

	private ExtensionRegistry $extensionRegistry;

	public function __construct(
		?Config $config = null,
		?Engine $engine = null,
		?ExtensionRegistry $extensionRegistry = null,
	) {
		$this->config = $config ?? new Config();
		$this->engine = $engine;
		$this->extensionRegistry = $extensionRegistry ?? ExtensionRegistry::default();
	}

	/**
	 * Set the stats engine for computing statistics after parsing.
	 *
	 * @return $this Fluent interface
	 */
	public function setEngine(Engine $engine): self
	{
		$this->engine = $engine;
		return $this;
	}

	/**
	 * Register an extension parser for a namespace URI.
	 *
	 * @param string $namespace The XML namespace URI
	 * @param string $parserClass Fully qualified class implementing ExtensionParserInterface
	 * @param string $prefix XML namespace prefix for serialization (e.g., 'gpxtpx')
	 * @return $this Fluent interface
	 */
	public function registerExtension(string $namespace, string $parserClass, string $prefix = 'ext'): self
	{
		$this->extensionRegistry->register($namespace, $parserClass, $prefix);
		return $this;
	}

	/**
	 * Load GPX file from path.
	 */
	public function load(string $path): GpxFile
	{
		return $this->parse(file_get_contents($path));
	}

	/**
	 * Parse GPX data string.
	 */
	public function parse(string $xml): GpxFile
	{
		$xmlElement = simplexml_load_string($xml);

		// Configure extension parser with our registry
		ExtensionParser::$registry = $this->extensionRegistry;

		$gpx = new GpxFile($this->config);

		$gpx->creator = isset($xmlElement['creator']) ? (string)$xmlElement['creator'] : null;
		$gpx->version = isset($xmlElement['version']) ? (string)$xmlElement['version'] : null;
		$gpx->metadata = isset($xmlElement->metadata) ? MetadataParser::parse($xmlElement->metadata) : null;
		$gpx->waypoints = isset($xmlElement->wpt) ? WaypointParser::parse($xmlElement->wpt) : [];
		$gpx->tracks = isset($xmlElement->trk) ? TrackParser::parse($xmlElement->trk) : [];
		$gpx->routes = isset($xmlElement->rte) ? RouteParser::parse($xmlElement->rte) : [];

		if ($this->engine !== null) {
			$gpx = $this->engine->process($gpx);
		}

		return $gpx;
	}

	/**
	 * Create library signature from name and version.
	 */
	public static function getSignature(): string
	{
		return sprintf("%s/%s", self::PACKAGE_NAME, self::VERSION);
	}
}