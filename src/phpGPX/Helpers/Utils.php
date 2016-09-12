<?php
/**
 * Created            05/09/16 17:02
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;


use phpGPX\Models\Point;
use phpGPX\Models\Summarizable;

class Utils
{

	public static function comparePointsByTimestamp(Point $point1, Point $point2)
	{
		if ($point1->timestamp == $point2->timestamp)
			return 0;
		return $point1->timestamp > $point2->timestamp;
	}

	public static function formatDateTime($datetime, $format = 'Y-m-d H:i:s', $timezone = 'UTC')
	{
		$formatted 				= null;

		if ($datetime instanceof \DateTime)
		{
			$datetime->setTimezone(new \DateTimeZone($timezone));
			$formatted 			= $datetime->format($format);
		}

		return $formatted;
	}

	/**
	 * @param Summarizable|Summarizable[] $object
	 * @return array|null
	 */
	public static function serialize($object) {
		if (is_array($object)) {
			$result = [];
			foreach  ($object as $record) {
				$result[] = $record->summary();
				$record = null;
			}
			$object = null;
			return $result;
		}
		else {
			return $object != null ? $object->summary() : null;
		}
	}

}