<?php
/**
 * ElevationGainLossCalculator.php
 *
 * @author Jens Hassler
 * @since  07/2018
 */

namespace phpGPX\Helpers;

use phpGPX\Config;
use phpGPX\Models\Point;

class ElevationGainLossCalculator
{
	/**
	 * @param Point[] $points
	 * @param Config $config
	 * @return array [cumulativeElevationGain, cumulativeElevationLoss]
	 */
	public static function calculate(array $points, Config $config): array
	{
		$cumulativeElevationGain = 0;
		$cumulativeElevationLoss = 0;

		$pointCount = count($points);

		$lastConsideredElevation = 0;

		for ($p = 0; $p < $pointCount; $p++) {
			$curElevation = $points[$p]->elevation;

			if ($curElevation === null) {
				continue;
			}

			if ($config->ignoreZeroElevation && $curElevation == 0) {
				continue;
			}

			if ($p === 0) {
				$lastConsideredElevation = $curElevation;
				continue;
			}

			$elevationDelta = $curElevation - $lastConsideredElevation;

			if ($config->applyElevationSmoothing &&
				abs($elevationDelta) > $config->elevationSmoothingThreshold &&
						($config->elevationSmoothingSpikesThreshold === null || abs($elevationDelta) < $config->elevationSmoothingSpikesThreshold)) {
				$cumulativeElevationGain += ($elevationDelta > 0) ? $elevationDelta : 0;
				$cumulativeElevationLoss += ($elevationDelta < 0) ? abs($elevationDelta) : 0;

				$lastConsideredElevation = $curElevation;
			}

			if (!$config->applyElevationSmoothing) {
				$cumulativeElevationGain += ($elevationDelta > 0) ? $elevationDelta : 0;
				$cumulativeElevationLoss += ($elevationDelta < 0) ? abs($elevationDelta) : 0;

				$lastConsideredElevation = $curElevation;
			}
		}

		return [$cumulativeElevationGain, $cumulativeElevationLoss];
	}
}