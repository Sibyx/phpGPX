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
	public static function getRawDistance(Point $point1, Point $point2)
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

	/**
	 * Returns distance between two points including elevation gain/loss
	 * @param Point $point1
	 * @param Point $point2
	 * @return float
	 */
	public static function getRealDistance(Point $point1, Point $point2)
	{
		$distance = self::getRawDistance($point1, $point2);

		$elevation1 = $point1->elevation != null ? $point1->elevation : 0;
		$elevation2 = $point2->elevation != null ? $point2->elevation : 0;
		$elevDiff = abs($elevation1 - $elevation2);

		return sqrt(pow($distance, 2) + pow($elevDiff, 2));
	}
}
