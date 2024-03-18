<?php
/**
 * DistanceCalculator.php
 *
 * @author miqwit
 * @since  03/2024
 * @version 1.0
 */

namespace phpGPX\Helpers;

use phpGPX\Models\Point;
use phpGPX\phpGPX;

/**
 * This helper will return the boundaries of a group of points,
 * e.g. the lowest latitude, lowest longitude, highest latitude, highest longitude.
 * This can be useful to display a segment on a map and to zoom the map
 * properly, so it shows all the points.
 */
class BoundsCalculator
{
	/**
	 * @return array of array Only two points with latitude and longitude that correspond to the
	 *   most northwestern and southeastern points of the track
	 */
	public static function calculate(array $points): array
	{
		$pointCount = count($points);

		$north = $east = -PHP_FLOAT_MAX; // look for longest lat and lon
		$south = $west = PHP_FLOAT_MAX; // look for shortest lat and lon

		for ($p = 0; $p < $pointCount; $p++) {
			$curPoint = $points[$p];

			$lng = $curPoint->longitude;
			$lat = $curPoint->latitude;

			// Update northWest and southEast points if needed
			if ($lat > $north) {$north = $lat;}
			if ($lng > $east) {$east = $lng;}
			if ($lat < $south) {$south = $lat;}
			if ($lng < $west) {$west = $lng;}
		}

		return [
			["lat" => $north, "lng" => $west],
			["lat" => $south, "lng" => $east]
		];
	}
}
