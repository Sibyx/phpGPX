<?php
/**
 * Created            30/08/16 17:27
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;

use phpGPX\Models\Point;

/**
 * Class GeoHelper
 * Geolocation methods.
 * @package phpGPX\Helpers
 */
abstract class GeoHelper
{
	const EARTH_RADIUS = 6371000;

	/**
	 * Returns distance in meters between two Points according to GPX coordinates.
	 * @see Point
	 * @param Point $point1
	 * @param Point $point2
	 * @return float
	 */
	public static function getDistance(Point $point1, Point $point2)
	{
		$latFrom = deg2rad($point1->latitude);
		$lonFrom = deg2rad($point1->longitude);
		$latTo = deg2rad($point2->latitude);
		$lonTo = deg2rad($point2->longitude);

		$lonDelta = $lonTo - $lonFrom;
		$a = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
		$angle = atan2(sqrt($a), $b);

		return $angle * self::EARTH_RADIUS;
	}
}
