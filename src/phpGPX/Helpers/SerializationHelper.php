<?php
/**
 * Created            14/02/2017 18:45
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;


use phpGPX\Models\Summarizable;

/**
 * Class SerializationHelper
 * Contains basic serialization helpers used in summary() methods.
 * @package phpGPX\Helpers
 */
abstract class SerializationHelper
{

	/**
	 * Returns integer or null.
	 * @param $value
	 * @return int|null
	 */
	public static function integerOrNull($value)
	{
		return is_null($value) ? null : (integer) $value;
	}

	/**
	 * Returns float or null.
	 * @param $value
	 * @return float|null
	 */
	public static function floatOrNull($value)
	{
		return is_null($value) ? null : (float) $value;
	}

	/**
	 * Returns double or null.
	 * @param $value
	 * @return double|null
	 */
	public static function doubleOrNull($value)
	{
		return is_null($value) ? null : (double) $value;
	}

	/**
	 * Returns string or null
	 * @param $value
	 * @return null|string
	 */
	public static function stringOrNull($value)
	{
		return empty($value) ? null : (string) $value;
	}

	/**
	 * Recursively traverse Summarizable objects and returns their array representation according summary() method.
	 * @param Summarizable|Summarizable[] $object
	 * @return array|null
	 */
	public static function serialize($object) {
		if (is_array($object)) {
			$result = [];
			foreach  ($object as $record) {
				$result[] = $record->toArray();
				$record = null;
			}
			$object = null;
			return $result;
		}
		else {
			return $object != null ? $object->toArray() : null;
		}
	}

}