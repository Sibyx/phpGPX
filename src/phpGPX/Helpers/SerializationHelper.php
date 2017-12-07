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
		return is_int($value) ? (integer) $value : null;
	}

	/**
	 * Returns float or null.
	 * @param $value
	 * @return float|null
	 */
	public static function floatOrNull($value)
	{
		return (is_float($value) || is_integer($value)) ? (float) $value : null;
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
	public static function serialize($object)
	{
		if (is_array($object)) {
			$result = [];
			foreach ($object as $record) {
				$result[] = $record->toArray();
				$record = null;
			}
			$object = null;
			return $result;
		} else {
			return $object != null ? $object->toArray() : null;
		}
	}
}
