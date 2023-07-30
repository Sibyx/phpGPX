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
use phpGPX\phpGPX;

class DistanceCalculator
{
	/**
	 * @var Point[]
	 */
	private array $points;

	/**
	 * DistanceCalculator constructor.
	 * @param Point[] $points
	 */
	public function __construct(array $points)
	{
		$this->points = $points;
	}

	public function getRawDistance(): float
    {
		return $this->calculate([GeoHelper::class, 'getRawDistance']);
	}

	public function getRealDistance(): float
    {
		return $this->calculate([GeoHelper::class, 'getRealDistance']);
	}

    /**
     * @param array $strategy
     * @return float
     */
	private function calculate(array $strategy): float
    {
		$distance = 0;

		$pointCount = count($this->points);

		$lastConsideredPoint = null;

		for ($p = 0; $p < $pointCount; $p++) {
			$curPoint = $this->points[$p];

			// skip the first point
			if ($p === 0) {
				$lastConsideredPoint = $curPoint;
				continue;
			}

			// calculate the delta from current point to last considered point
			$curPoint->difference = call_user_func($strategy, $lastConsideredPoint, $curPoint);

			// if smoothing is applied we only consider points with a delta above the threshold (e.g. 2 meters)
			if (phpGPX::$APPLY_DISTANCE_SMOOTHING) {
				$differenceFromLastConsideredPoint = call_user_func($strategy, $curPoint, $lastConsideredPoint);

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
