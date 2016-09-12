<?php
/**
 * Created            05/09/16 17:02
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;


use phpGPX\Model\Point;

class Utils
{

	public static function comparePointsByTimestamp(Point $point1, Point $point2)
	{
		if ($point1->timestamp == $point2->timestamp)
			return 0;
		return $point1->timestamp > $point2->timestamp;
	}

}