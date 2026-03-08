<?php
/**
 * ElevationGainLossCalculator.php
 *
 * @author Jens Hassler
 * @since  07/2018
 */

namespace phpGPX\Helpers;

use phpGPX\Models\Point;

class ElevationGainLossCalculator
{
	/**
	 * @param Point[] $points
	 * @return array [cumulativeElevationGain, cumulativeElevationLoss]
	 */
	public static function calculate(
		array $points,
		bool $ignoreZeroElevation = false,
		bool $applySmoothing = false,
		int $smoothingThreshold = 2,
		?int $spikesThreshold = null,
	): array {
		$cumulativeElevationGain = 0;
		$cumulativeElevationLoss = 0;

		$pointCount = count($points);

		$lastConsideredElevation = 0;

		for ($p = 0; $p < $pointCount; $p++) {
			$curElevation = $points[$p]->elevation;

			if ($curElevation === null) {
				continue;
			}

			if ($ignoreZeroElevation && $curElevation == 0) {
				continue;
			}

			if ($p === 0) {
				$lastConsideredElevation = $curElevation;
				continue;
			}

			$elevationDelta = $curElevation - $lastConsideredElevation;

			if ($applySmoothing &&
				abs($elevationDelta) > $smoothingThreshold &&
						($spikesThreshold === null || abs($elevationDelta) < $spikesThreshold)) {
				$cumulativeElevationGain += ($elevationDelta > 0) ? $elevationDelta : 0;
				$cumulativeElevationLoss += ($elevationDelta < 0) ? abs($elevationDelta) : 0;

				$lastConsideredElevation = $curElevation;
			}

			if (!$applySmoothing) {
				$cumulativeElevationGain += ($elevationDelta > 0) ? $elevationDelta : 0;
				$cumulativeElevationLoss += ($elevationDelta < 0) ? abs($elevationDelta) : 0;

				$lastConsideredElevation = $curElevation;
			}
		}

		return [$cumulativeElevationGain, $cumulativeElevationLoss];
	}
}