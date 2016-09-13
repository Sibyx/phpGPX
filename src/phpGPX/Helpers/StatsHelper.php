<?php
/**
 * Created            30/08/16 17:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;


use phpGPX\Models\Collection;
use phpGPX\Models\Segment;

abstract class StatsHelper
{

	/**
	 * @param Collection|Segment $object
	 */
	public static function recalculateStats($object)
	{
		switch (get_class($object))
		{
			case Collection::class:
				self::createStatsForCollection($object);
				break;
			case Segment::class:
				self::createStatsForSegment($object);
				break;
		}
	}

	/**
	 * @param Collection $collection
	 */
	private static function createStatsForCollection(Collection &$collection)
	{
		$segmentsCount = count($collection->segments);
		$collection->stats->reset();

		if (empty($collection->segments) || empty($collection->segments[0]->points))
			return;

		$firstSegment = &$collection->segments[0];
		$firstPoint = &$collection->segments[0]->points[0];

		$lastSegment = end($collection->segments);
		$lastPoint = end(end($collection->segments)->points);

		$collection->stats->startedAt = $firstPoint->timestamp;
		$collection->stats->finishedAt = $lastPoint->timestamp;
		$collection->stats->minAltitude = $firstPoint->altitude;

		for ($s = 0; $s < $segmentsCount; $s++)
		{
			self::createStatsForSegment($collection->segments[$s]);
			$pointCount = count($collection->segments[$s]->points);

			for ($p = 0; $p <$pointCount; $p++)
			{
				if (($p == 0) && ($s > 0))
				{
					$collection->segments[$s]->points[$p]->difference = GeoHelper::getDistance(end($collection->segments[$s-1]->points), $collection->segments[$s]->points[$p]);
				}
				elseif ($p > 0)
				{
					$collection->segments[$s]->points[$p]->difference = GeoHelper::getDistance($collection->segments[$s]->points[$p-1], $collection->segments[$s]->points[$p]);
				}

				$collection->stats->distance += $collection->segments[$s]->points[$p]->difference;
				$collection->segments[$s]->points[$p]->distance = $collection->stats->distance;
			}

			if ($collection->stats->minAltitude == null)
			{
				$collection->stats->minAltitude = $collection->segments[$s]->stats->minAltitude;
			}

			if ($collection->stats->maxAltitude < $collection->segments[$s]->stats->maxAltitude)
			{
				$collection->stats->maxAltitude = $collection->segments[$s]->stats->maxAltitude;
			}

			if ($collection->stats->minAltitude > $collection->segments[$s]->stats->minAltitude)
			{
				$collection->stats->minAltitude = $collection->segments[$s]->stats->minAltitude;
			}
		}

		//TODO: mozno bude lepsie overovanie podla typu: TRACK, WAYPOINT, ROUTE pretoze to vychadza zo standardu
		if (isset($firstPoint->timestamp) && $firstPoint->timestamp instanceof \DateTime)
		{
			$collection->stats->duration = $lastPoint->timestamp->getTimestamp() - $firstPoint->timestamp->getTimestamp();

			if ($collection->stats->duration != 0)
			{
				$collection->stats->averageSpeed = $collection->stats->distance / $collection->stats->duration;
			}

			if ($collection->stats->distance != 0)
			{
				$collection->stats->averagePace = $collection->stats->duration / ($collection->stats->distance / 1000);
			}
		}


	}

	/**
	 * @param Segment $segment
	 */
	private static function createStatsForSegment(Segment &$segment)
	{

		$count = count($segment->points);
		$segment->stats->reset();

		if (empty($segment->points))
			return;

		$firstPoint = &$segment->points[0];
		$lastPoint = end($segment->points);

		$segment->stats->startedAt = $firstPoint->timestamp;
		$segment->stats->finishedAt = $lastPoint->timestamp;
		$segment->stats->minAltitude = $firstPoint->altitude;

		for ($i = 0; $i < $count; $i++)
		{
			if ($i > 0)
			{
				$segment->stats->distance += GeoHelper::getDistance($segment->points[$i-1], $segment->points[$i]);
			}

			if ($segment->stats->maxAltitude < $segment->points[$i]->altitude)
			{
				$segment->stats->maxAltitude = $segment->points[$i]->altitude;
			}

			if ($segment->stats->minAltitude > $segment->points[$i]->altitude)
			{
				$segment->stats->minAltitude = $segment->points[$i]->altitude;
			}
		}

		//TODO: mozno bude lepsie overovanie podla typu: TRACK, WAYPOINT, ROUTE pretoze to vychadza zo standardu
		if (isset($firstPoint->timestamp) && $firstPoint->timestamp instanceof \DateTime)
		{
			$segment->stats->duration = $lastPoint->timestamp->getTimestamp() - $firstPoint->timestamp->getTimestamp();

			if ($segment->stats->duration != 0)
			{
				$segment->stats->averageSpeed = $segment->stats->distance / $segment->stats->duration;
			}

			if ($segment->stats->distance != 0)
			{
				$segment->stats->averagePace = $segment->stats->duration / ($segment->stats->distance / 1000);
			}
		}
	}

}