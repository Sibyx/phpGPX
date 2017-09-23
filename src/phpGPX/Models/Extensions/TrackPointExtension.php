<?php
/**
 * Created            26/08/16 17:05
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models\Extensions;
use phpGPX\Helpers\SerializationHelper;

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
	 * @var float
	 */
	public $aTemp;

	/**
	 * @var float
	 */
	public $wTemp;

	/**
	 * Depth in meters.
	 * @var float
	 */
	public $depth;

	/**
	 * Heart rate in beats per minute.
	 * @var float
	 */
	public $hearthRate;

	/**
	 * Cadence in revolutions per minute.
	 * @var float
	 */
	public $cadence;

	/**
	 * Speed in meters per second.
	 * @var float
	 */
	public $speed;

	/**
	 * Course. This type contains an angle measured in degrees in a clockwise direction from the true north line.
	 * @var int
	 */
	public $course;

	/**
	 * Bearing. This type contains an angle measured in degrees in a clockwise direction from the true north line.
	 * @var int
	 */
	public $bearing;

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
	public function toArray()
	{
		return [
			'aTemp' => SerializationHelper::floatOrNull($this->aTemp),
			'wTemp' => SerializationHelper::floatOrNull($this->wTemp),
			'depth' => SerializationHelper::floatOrNull($this->depth),
			'hr' => SerializationHelper::floatOrNull($this->hearthRate),
			'cad' => SerializationHelper::floatOrNull($this->cadence),
			'speed' => SerializationHelper::floatOrNull($this->speed),
			'course' => SerializationHelper::integerOrNull($this->course),
			'bearing' => SerializationHelper::integerOrNull($this->bearing)
		];
	}
}