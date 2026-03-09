<?php

/**
 * Created            05/09/16 17:02
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Helpers;

/**
 * Class DateTimeHelper
 * @package phpGPX\Helpers
 */
class DateTimeHelper
{
	/**
	 * @param $datetime
	 * @param string $format
	 * @param string|null $timezone
	 * @return null|string
	 * @throws \Exception
	 */
	public static function formatDateTime($datetime, string $format = 'c', ?string $timezone = 'UTC'): ?string
	{
		$formatted = null;

		if ($datetime instanceof \DateTime) {
			$datetime->setTimezone(new \DateTimeZone($timezone ?? 'UTC'));
			$formatted = $datetime->format($format);
		}

		return $formatted;
	}

	/**
	 * @param $value
	 * @param string $timezone
	 * @return \DateTime
	 * @throws \Exception
	 */
	public static function parseDateTime($value, string $timezone = 'Europe/London'): \DateTime
	{
		$timezone = new \DateTimeZone($timezone);
		$datetime = new \DateTime($value, $timezone);
		$datetime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		return $datetime;
	}
}
