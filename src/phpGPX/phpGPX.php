<?php
/**
 * Created            26/08/16 13:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX;

use phpGPX\Helpers\DateTimeHelper;
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

	public function __construct(?Config $config = null)
	{
		$this->config = $config ?? new Config();
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

		if ($this->config->sortByTimestamp) {
			$this->sortPointsByTimestamp($gpx);
		}

		if ($this->config->calculateStats) {
			foreach ($gpx->tracks as $track) {
				$track->recalculateStats($this->config);
			}
			foreach ($gpx->routes as $route) {
				$route->recalculateStats($this->config);
			}
		}

		return $gpx;
	}

	/**
	 * Sort all point arrays in-place by timestamp.
	 */
	private function sortPointsByTimestamp(GpxFile $gpx): void
	{
		foreach ($gpx->tracks as $track) {
			foreach ($track->segments as $segment) {
				if (!empty($segment->points) && $segment->points[0]->time !== null) {
					usort($segment->points, [DateTimeHelper::class, 'comparePointsByTimestamp']);
				}
			}
		}

		foreach ($gpx->routes as $route) {
			if (!empty($route->points) && $route->points[0]->time !== null) {
				usort($route->points, [DateTimeHelper::class, 'comparePointsByTimestamp']);
			}
		}
	}

	/**
	 * Create library signature from name and version.
	 */
	public static function getSignature(): string
	{
		return sprintf("%s/%s", self::PACKAGE_NAME, self::VERSION);
	}
}