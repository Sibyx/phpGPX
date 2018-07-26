<?php
/**
 * Created            17/02/2017 18:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Helpers\GeoHelper;
use phpGPX\Helpers\SerializationHelper;
use phpGPX\phpGPX;

/**
 * Class Track
 * @package phpGPX\Models
 */
class Track extends Collection
{

	/**
	 * Array of Track segments
	 * @var Segment[]
	 */
	public $segments;

	/**
	 * Track constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->segments = [];
	}


	/**
	 * Return all points in collection.
	 * @return Point[]
	 */
	public function getPoints()
	{
		/** @var Point[] $points */
		$points = [];

		foreach ($this->segments as $segment) {
			$points = array_merge($points, $segment->points);
		}

		if (phpGPX::$SORT_BY_TIMESTAMP && !empty($points)) {
			usort($points, array('phpGPX\Helpers\DateTimeHelper', 'comparePointsByTimestamp'));
		}

		return $points;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'name' => SerializationHelper::stringOrNull($this->name),
			'cmt' => SerializationHelper::stringOrNull($this->comment),
			'desc' => SerializationHelper::stringOrNull($this->description),
			'src' => SerializationHelper::stringOrNull($this->source),
			'link' => SerializationHelper::serialize($this->links),
			'number' => SerializationHelper::integerOrNull($this->number),
			'type' => SerializationHelper::stringOrNull($this->type),
			'extensions' => SerializationHelper::serialize($this->extensions),
			'trkseg' => SerializationHelper::serialize($this->segments),
			'stats' => SerializationHelper::serialize($this->stats)
		];
	}

	/**
	 * Recalculate stats objects.
	 * @return void
	 */
	public function recalculateStats()
	{
		if (empty($this->stats)) {
			$this->stats = new Stats();
		}

		$this->stats->reset();

		if (empty($this->segments)) {
			return;
		}

		$segmentsCount = count($this->segments);

		$firstSegment = null;
		$firstPoint = null;
		
		// Identify first Segment/Point
		for ($s = 0; $s < $segmentsCount; $s++) {
			$pointCount = count($this->segments[$s]->points);
			for ($p = 0; $p < $pointCount; $p++) {
				if (is_null($firstPoint)) {
					$firstPoint = &$this->segments[$s]->points[$p];
					$firstSegment = &$this->segments[$s];
					break;
				}
			}
		}

		if (empty($firstPoint)) {
			return;
		}

		$lastSegment = end($this->segments);
		$lastPoint = end(end($this->segments)->points);

		$this->stats->startedAt = $firstPoint->time;
		$this->stats->finishedAt = $lastPoint->time;
		$this->stats->minAltitude = $firstPoint->elevation;

		for ($s = 0; $s < $segmentsCount; $s++) {
			$this->segments[$s]->recalculateStats();
            $this->calculateElevationGainAndLoss($this->segments[$s]);

			if ($this->stats->minAltitude === null) {
				$this->stats->minAltitude = $this->segments[$s]->stats->minAltitude;
			}
			if ($this->stats->maxAltitude < $this->segments[$s]->stats->maxAltitude) {
				$this->stats->maxAltitude = $this->segments[$s]->stats->maxAltitude;
			}
			if ($this->stats->minAltitude > $this->segments[$s]->stats->minAltitude) {
				$this->stats->minAltitude = $this->segments[$s]->stats->minAltitude;
			}
		}

		$allPoints = $this->getPoints();
		$allPtsCnt = count($allPoints);
		if ($allPtsCnt > 0) {
		    $lastConsideredPoint = $allPoints[0];
			for ($p = 1; $p < $allPtsCnt; $p++) {
                // skipping first point
                $allPoints[$p]->difference = GeoHelper::getDistance($allPoints[$p-1], $allPoints[$p]);

			    if (phpGPX::$APPLY_DISTANCE_SMOOTHING) {
			        $differenceFromLastConsideredPoint = GeoHelper::getDistance($allPoints[$p], $lastConsideredPoint);
			        if ($differenceFromLastConsideredPoint > phpGPX::$DISTANCE_SMOOTHING_THRESHOLD) {
			            $lastConsideredPoint = $allPoints[$p];
                        $this->stats->distance += $differenceFromLastConsideredPoint;
                    }
                } else {
                    $this->stats->distance += $allPoints[$p]->difference;
                }
                $allPoints[$p]->distance = $this->stats->distance;
			}
		}

		if (($firstPoint->time instanceof \DateTime) && ($lastPoint->time instanceof \DateTime)) {
			$this->stats->duration = $lastPoint->time->getTimestamp() - $firstPoint->time->getTimestamp();

			if ($this->stats->duration != 0) {
				$this->stats->averageSpeed = $this->stats->distance / $this->stats->duration;
			}

			if ($this->stats->distance != 0) {
				$this->stats->averagePace = $this->stats->duration / ($this->stats->distance / 1000);
			}
		}
	}

    /**
     * @param Segment $segment
     * @return void
     */
    private function calculateElevationGainAndLoss(Segment $segment)
    {
        $lastConsideredElevation = 0;
        $pointCount = count($segment->points);
        for ($p = 0; $p < $pointCount; $p++) {
            if ($segment->points[$p]->elevation !== null) {
                if ($this->stats->cumulativeElevationGain === null) {
                    $lastConsideredElevation = $segment->points[$p]->elevation;
                    $this->stats->cumulativeElevationGain = 0;
                    $this->stats->cumulativeElevationLoss = 0;
                } else {
                    $curElevation = $segment->points[$p]->elevation;
                    if ($curElevation != 0) {
                        if (phpGPX::$APPLY_ELEVATION_SMOOTHING &&
                            abs($curElevation - $lastConsideredElevation) > phpGPX::$ELEVATION_SMOOTHING_THRESHOLD) {
                            $this->addElevationDelta($curElevation - $lastConsideredElevation);
                            $lastConsideredElevation = $curElevation;
                        } elseif (!phpGPX::$APPLY_ELEVATION_SMOOTHING) {
                            $this->addElevationDelta($curElevation - $lastConsideredElevation);
                            $lastConsideredElevation = $segment->points[$p]->elevation;
                        }
                    }
                }
            }
        }
    }


    /**
     * @param float $elevationDelta
     * @return mixed
     */
    private function addElevationDelta($elevationDelta)
    {
        $this->stats->cumulativeElevationGain += ($elevationDelta > 0) ? $elevationDelta : 0;
        $this->stats->cumulativeElevationLoss += ($elevationDelta < 0) ? abs($elevationDelta) : 0;

        return $elevationDelta;
    }
}
