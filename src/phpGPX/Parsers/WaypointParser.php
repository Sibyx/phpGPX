<?php
/**
 * Created            10/02/2017 15:44
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

/**
 * Class WaypointParser
 * @package phpGPX\Parsers
 */
abstract class WaypointParser
{

	/**
	 * @param \SimpleXMLElement $nodes - a non empty list of wpt elements
	 * @return array
	 */
	public static function parse(\SimpleXMLElement $nodes)
	{
		$points = [];

		// foreach ($nodes->wpt as $item) this was incorrect, the ->wpt was already done in the caller
		foreach ($nodes as $item) {
			$point = PointParser::parse($item);

			if ($point) {
				$points[] = $point;
			}
		}

		return $points;
	}
}
