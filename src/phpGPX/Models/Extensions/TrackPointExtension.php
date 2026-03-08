<?php
/**
 * Created            26/08/16 17:05
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models\Extensions;

/**
 * Garmin TrackPointExtension model (v2).
 *
 * Provides sensor data per track point: heart rate, cadence, temperature, etc.
 *
 * @see https://www8.garmin.com/xmlschemas/TrackPointExtensionv2.xsd
 */
class TrackPointExtension extends AbstractExtension implements ExtensionInterface
{
	const NAMESPACE_URI = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v2';
	const SCHEMA_LOCATION = 'http://www.garmin.com/xmlschemas/TrackPointExtensionv2.xsd';
	const TAG_NAME = 'TrackPointExtension';

	public static function getNamespace(): string { return self::NAMESPACE_URI; }
	public static function getSchemaLocation(): string { return self::SCHEMA_LOCATION; }
	public static function getTagName(): string { return self::TAG_NAME; }

	/** Air temperature in degrees Celsius. */
	public ?float $aTemp = null;

	/** Water temperature in degrees Celsius. */
	public ?float $wTemp = null;

	/** Depth in meters. */
	public ?float $depth = null;

	/** Heart rate in beats per minute. */
	public ?float $hr = null;

	/** Cadence in revolutions per minute. */
	public ?float $cad = null;

	/** Speed in meters per second. */
	public ?float $speed = null;

	/** Course in degrees from true north. */
	public ?int $course = null;

	/** Bearing in degrees from true north. */
	public ?int $bearing = null;

	public function jsonSerialize(): array
	{
		return array_filter([
			'aTemp' => $this->aTemp,
			'wTemp' => $this->wTemp,
			'depth' => $this->depth,
			'hr' => $this->hr,
			'cad' => $this->cad,
			'speed' => $this->speed,
			'course' => $this->course,
			'bearing' => $this->bearing,
		], fn($v) => $v !== null);
	}
}