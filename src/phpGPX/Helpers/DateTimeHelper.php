<?php
/**
 * Created            05/09/16 17:02
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;


use phpGPX\Models\Point;

class DateTimeHelper
{

	public static function comparePointsByTimestamp(Point $point1, Point $point2)
	{
		if ($point1->time == $point2->time)
			return 0;
		return $point1->time > $point2->time;
	}

	public static function formatDateTime($datetime, $format = 'c', $timezone = 'UTC')
	{
		$formatted 				= null;

		if ($datetime instanceof \DateTime)
		{
			$datetime->setTimezone(new \DateTimeZone($timezone));
			$formatted 			= $datetime->format($format);
		}

		return $formatted;
	}

	public static function parseDateTime($value, $timezone = 'UTC')
	{
		$timezone = new \DateTimeZone($timezone);
		$datetime = new \DateTime($value, $timezone);
		$datetime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		return $datetime;
	}
}