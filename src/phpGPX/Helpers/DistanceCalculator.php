<?php
/**
 * DistanceCalculator.php
 *
 * @author Jens Hassler
 * @since  07/2018
 */

namespace phpGPX\Helpers;

use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\Point;
use phpGPX\phpGPX;

class DistanceCalculator
{
	/**
	 * @param Point[]|array $points
	 * @return float
	 */
	public static function calculate(array $points)
	{
		$distance = 0;

		$pointCount = count($points);

		$lastConsideredPoint = null;

		for ($p = 0; $p < $pointCount; $p++) {
			$curPoint = $points[$p];

			// skip the first point
			if ($p === 0) {
				$lastConsideredPoint = $curPoint;
				continue;
			}

			// calculate the delta from current point to last considered point
			$curPoint->difference = GeoHelper::getDistance($lastConsideredPoint, $curPoint);

			// if smoothing is applied we only consider points with a delta above the threshold (e.g. 2 meters)
			if (phpGPX::$APPLY_DISTANCE_SMOOTHING) {
				$differenceFromLastConsideredPoint = GeoHelper::getDistance($curPoint, $lastConsideredPoint);

				if ($differenceFromLastConsideredPoint > phpGPX::$DISTANCE_SMOOTHING_THRESHOLD) {
					$distance += $differenceFromLastConsideredPoint;
					$lastConsideredPoint = $curPoint;
				}
			}

			// if smoothing is not applied we consider every point
			else {
				$distance += $curPoint->difference;
				$lastConsideredPoint = $curPoint;
			}

			$curPoint->distance = $distance;
		}

		return $distance;
	}
}
