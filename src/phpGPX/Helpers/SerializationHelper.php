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
	public static function integerOrNull($value): ?int
    {
		return is_numeric($value) ? (integer) $value : null;
	}

	/**
	 * Returns float or null.
	 * @param $value
	 * @return float|null
	 */
	public static function floatOrNull($value): ?float
    {
		return is_numeric($value) ? (float) $value : null;
	}

	/**
	 * Returns string or null
	 * @param $value
	 * @return null|string
	 */
	public static function stringOrNull($value): ?string
    {
		return is_string($value) ? $value : null;
	}

	/**
	 * Recursively traverse Summarizable objects and returns their array representation according summary() method.
	 * @param Summarizable|Summarizable[] $object
	 * @return array|null
	 */
	public static function serialize(Summarizable|array|null $object): ?array
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
			return $object?->toArray();
		}
	}

	public static function filterNotNull(array $array): array
    {
		foreach ($array as &$item) {
			if (!is_array($item)) {
				continue;
			}
			
			$item = self::filterNotNull($item);
		}

		return array_filter($array, function ($item) {
			return $item !== null && (!is_array($item) || count($item));
		});
	}
}
