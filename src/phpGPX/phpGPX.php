<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;

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

	const PACKAGE_NAME = 'phpGPX';
	const VERSION = '2.0.0-alpha.1';

	/**
	 * Load GPX file.
	 * @param string $path
	 * @return GpxFile
	 */
	public static function load(string $path): GpxFile
	{
		$xml = file_get_contents($path);

		return self::parse($xml);
	}

	/**
	 * Parse GPX data string.
	 * @param string $xml
	 * @return GpxFile
	 */
	public static function parse(string $xml): GpxFile
	{
		$xml = simplexml_load_string($xml);

		$gpx = new GpxFile();

		// Parse creator
		$gpx->creator = isset($xml['creator']) ? (string)$xml['creator'] : null;

		// Parse metadata
		$gpx->metadata = isset($xml->metadata) ? MetadataParser::parse($xml->metadata) : null;

		// Parse waypoints
		$gpx->waypoints = isset($xml->wpt) ? WaypointParser::parse($xml->wpt) : [];

		// Parse tracks
		$gpx->tracks = isset($xml->trk) ? TrackParser::parse($xml->trk) : [];

		// Parse routes
		$gpx->routes = isset($xml->rte) ? RouteParser::parse($xml->rte) : [];

		return $gpx;
	}

	/**
	 * Create library signature from name and version.
	 * @return string
	 */
	public static function getSignature(): string
	{
		return sprintf("%s/%s", self::PACKAGE_NAME, self::VERSION);
	}
}
