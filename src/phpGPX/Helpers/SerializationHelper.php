<?php

/**
 * Created            14/02/2017 18:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;

/**
 * Class SerializationHelper
 * Contains basic serialization helpers used in serialization methods.
 * @package phpGPX\Helpers
 */
abstract class SerializationHelper
{
	/**
	 * Build a GeoJSON position array [lon, lat] or [lon, lat, ele].
	 * @param float|null $longitude
	 * @param float|null $latitude
	 * @param float|null $elevation
	 * @return array
	 */
	public static function position(?float $longitude, ?float $latitude, ?float $elevation = null): array
	{
		$pos = [(float) $longitude, (float) $latitude];
		if ($elevation !== null) {
			$pos[] = $elevation;
		}
		return $pos;
	}
}
