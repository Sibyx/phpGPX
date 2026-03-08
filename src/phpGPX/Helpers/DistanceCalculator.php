<?php
/**
 * DistanceCalculator.php
 *
 * @author Jens Hassler
 * @author Jakub Dubec
 * @since  07/2018
 * @version 2.0
 */

namespace phpGPX\Helpers;

use phpGPX\Models\Point;

class DistanceCalculator
{
	/**
	 * @param Point[] $points
	 */
	public function __construct(
		private array $points,
		private bool $applySmoothing = false,
		private int $smoothingThreshold = 2,
	) {}

	public function getRawDistance(): float
	{
		return $this->calculate([GeoHelper::class, 'getRawDistance']);
	}

	public function getRealDistance(): float
	{
		return $this->calculate([GeoHelper::class, 'getRealDistance']);
	}

	private function calculate(array $strategy): float
	{
		$distance = 0;

		$pointCount = count($this->points);

		$lastConsideredPoint = null;

		for ($p = 0; $p < $pointCount; $p++) {
			$curPoint = $this->points[$p];

			if ($p === 0) {
				$lastConsideredPoint = $curPoint;
				continue;
			}

			$curPoint->difference = call_user_func($strategy, $lastConsideredPoint, $curPoint);

			if ($this->applySmoothing) {
				$differenceFromLastConsideredPoint = call_user_func($strategy, $curPoint, $lastConsideredPoint);

				if ($differenceFromLastConsideredPoint > $this->smoothingThreshold) {
					$distance += $differenceFromLastConsideredPoint;
					$lastConsideredPoint = $curPoint;
				}
			} else {
				$distance += $curPoint->difference;
				$lastConsideredPoint = $curPoint;
			}

			$curPoint->distance = $distance;
		}

		return $distance;
	}
}