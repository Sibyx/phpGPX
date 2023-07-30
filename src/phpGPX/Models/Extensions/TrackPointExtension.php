<?php
/**
 * Created            26/08/16 17:05
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models\Extensions;

/**
 * Class TrackPointExtension
 * Extension version: v2
 * Based on namespace: http://www.garmin.com/xmlschemas/TrackPointExtensionv2.xsd
 * @package phpGPX\Models\Extensions
 */
class TrackPointExtension extends AbstractExtension
{
	const EXTENSION_V1_NAMESPACE = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1';
	const EXTENSION_V1_NAMESPACE_XSD = 'http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd';

	const EXTENSION_NAMESPACE = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v2';
	const EXTENSION_NAMESPACE_XSD = 'http://www.garmin.com/xmlschemas/TrackPointExtensionv2.xsd';

	const EXTENSION_NAME = 'TrackPointExtension';
	const EXTENSION_NAMESPACE_PREFIX = 'gpxtpx';

	/**
	 * Average temperature value measured in degrees Celsius.
	 * @var float|null
     */
	public ?float $aTemp;

	/**
	 * @var float|null
     */
	public ?float $wTemp;

	/**
	 * Depth in meters.
	 * @var float|null
     */
	public ?float $depth;

	/**
	 * Heart rate in beats per minute.
	 * @since v1.0RC3
	 * @var float|null
     */
	public ?float $hr;

	/**
	 * Cadence in revolutions per minute.
	 * @var float|null
     */
	public ?float $cad;

	/**
	 * Speed in meters per second.
	 * @var float|null
	 */
	public ?float $speed;

	/**
	 * Course. This type contains an angle measured in degrees in a clockwise direction from the true north line.
	 * @var int|null
     */
	public ?int $course;

	/**
	 * Bearing. This type contains an angle measured in degrees in a clockwise direction from the true north line.
	 * @var int|null
	 */
	public ?int $bearing;

	/**
	 * TrackPointExtension constructor.
	 */
	public function __construct()
	{
		parent::__construct(self::EXTENSION_NAMESPACE, self::EXTENSION_NAME);
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray(): array
    {
		return [
			'aTemp' => $this->aTemp ?? null,
			'wTemp' => $this->wTemp ?? null,
			'depth' => $this->depth ?? null,
			'hr' => $this->hr ?? null,
			'cad' => $this->cad ?? null,
			'speed' => $this->speed ?? null,
			'course' => $this->course ?? null,
			'bearing' => $this->bearing ?? null
		];
	}
}
