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
	const GEOJSON_FORMAT = 'geojson';

	const PACKAGE_NAME = 'phpGPX';
	const VERSION = '2.0.0-alpha.1';

	/**
	 * Pretty print XML output
	 * @var bool
	 */
	public static bool $PRETTY_PRINT = true;

	/**
	 * Ignore elevation values of 0
	 * @var bool
	 */
	public static bool $IGNORE_ELEVATION_0 = false;

	/**
	 * Calculate stats for tracks, segments and routes
	 * @var bool
	 */
	public static bool $CALCULATE_STATS = true;

	/**
	 * DateTime format for output
	 * @var string
	 */
	public static string $DATETIME_FORMAT = 'c';

	/**
	 * DateTime timezone output
	 * @var string|null
	 */
	public static ?string $DATETIME_TIMEZONE_OUTPUT = null;

	/**
	 * Additional sort based on timestamp in Routes & Tracks on XML read.
	 * @var bool
	 */
	public static bool $SORT_BY_TIMESTAMP = false;

	/**
	 * Apply elevation gain/loss smoothing
	 * @var bool
	 */
	public static bool $APPLY_ELEVATION_SMOOTHING = false;

	/**
	 * Minimum elevation difference threshold in meters for smoothing
	 * @var int
	 */
	public static int $ELEVATION_SMOOTHING_THRESHOLD = 2;

	/**
	 * Maximum elevation difference threshold in meters for spike filtering
	 * @var int|null
	 */
	public static ?int $ELEVATION_SMOOTHING_SPIKES_THRESHOLD = null;

	/**
	 * Apply distance calculation smoothing
	 * @var bool
	 */
	public static bool $APPLY_DISTANCE_SMOOTHING = false;

	/**
	 * Minimum distance threshold in meters for smoothing
	 * @var int
	 */
	public static int $DISTANCE_SMOOTHING_THRESHOLD = 2;

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
