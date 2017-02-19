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
	 * @param \SimpleXMLElement $node
	 * @return array
	 */
	public static function parse(\SimpleXMLElement $node)
	{
		$points = [];

		foreach ($node->wpt as $item)
		{
			$point = PointParser::parse($item);

			if ($point)
			{
				$points[] = $point;
			}
		}

		return $points;
	}
}	