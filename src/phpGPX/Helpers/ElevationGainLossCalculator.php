<?php
/**
 * ElevationGainLossCalculator.php
 *
 * @author Jens Hassler
 * @since  07/2018
 */

namespace phpGPX\Helpers;

use phpGPX\Models\Point;
use phpGPX\phpGPX;

class ElevationGainLossCalculator
{
	/**
	 * @param Point[]|array $points
	 * @return array
	 */
	public static function calculate(array $points)
	{
		$cumulativeElevationGain = 0;
		$cumulativeElevationLoss = 0;

		$pointCount = count($points);

		$lastConsideredElevation = 0;

		for ($p = 0; $p < $pointCount; $p++) {
			$curElevation = $points[$p]->elevation;

			// skip points with empty elevation
			if ($curElevation === null) {
				continue;
			}

			// skip points with 0 elevation if configuration allows
			if (phpGPX::$IGNORE_ELEVATION_0 && $curElevation == 0) {
				continue;
			}

			// skip the first point
			if ($p === 0) {
				$lastConsideredElevation = $curElevation;
				continue;
			}

			// calculate the delta from current point to last considered point
			$elevationDelta = $curElevation - $lastConsideredElevation;

			// if smoothing is applied we only consider points with a delta above the threshold (e.g. 2 meters)
			if (phpGPX::$APPLY_ELEVATION_SMOOTHING &&
				abs($elevationDelta) > phpGPX::$ELEVATION_SMOOTHING_THRESHOLD &&
						(phpGPX::$ELEVATION_SMOOTHING_SPIKES_THRESHOLD === null || abs($elevationDelta) < phpGPX::$ELEVATION_SMOOTHING_SPIKES_THRESHOLD)) {
				$cumulativeElevationGain += ($elevationDelta > 0) ? $elevationDelta : 0;
				$cumulativeElevationLoss += ($elevationDelta < 0) ? abs($elevationDelta) : 0;

				$lastConsideredElevation = $curElevation;
			}

			// if smoothing is not applied we consider every point
			if (!phpGPX::$APPLY_ELEVATION_SMOOTHING) {
				$cumulativeElevationGain += ($elevationDelta > 0) ? $elevationDelta : 0;
				$cumulativeElevationLoss += ($elevationDelta < 0) ? abs($elevationDelta) : 0;

				$lastConsideredElevation = $curElevation;
			}
		}

		return [$cumulativeElevationGain, $cumulativeElevationLoss];
	}
}
