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
	 * Recursively traverse objects and returns their array representation.
	 * If the object has a toArray method, it will be used, otherwise jsonSerialize will be used.
	 * @param \JsonSerializable|array|null $object
	 * @return array|null
	 */
	public static function serialize(\JsonSerializable|array|null $object): ?array
    {
		if (is_array($object)) {
			$result = [];
			foreach ($object as $record) {
				if (method_exists($record, 'toArray')) {
					$result[] = $record->toArray();
				} else {
					$result[] = $record->jsonSerialize();
				}
				$record = null;
			}
			$object = null;
			return $result;
		} else {
			if ($object !== null && method_exists($object, 'toArray')) {
				return $object->toArray();
			}
			return $object?->jsonSerialize();
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
