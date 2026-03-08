<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;

use phpGPX\Analysis\Engine;
use phpGPX\Models\GpxFile;
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
	const VERSION = '2.0.0-alpha.2';

	public readonly Config $config;

	private ?Engine $engine = null;

	public function __construct(?Config $config = null, ?Engine $engine = null)
	{
		$this->config = $config ?? new Config();
		$this->engine = $engine;
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

		$gpx = new GpxFile($this->config);

		$gpx->creator = isset($xmlElement['creator']) ? (string)$xmlElement['creator'] : null;
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