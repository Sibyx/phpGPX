<?php
/**
 * Created            26/08/16 17:05
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models\Extensions;

/**
 * Class TrackPointExtension
 * TODO: https://www8.garmin.com/xmlschemas/TrackPointExtensionv1.xsd
 * @package phpGPX\Models\Extensions
 */
class TrackPointExtension extends AbstractExtension
{
	const EXTENSION_NAMESPACE = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1';
	const EXTENSION_NAMESPACE_XSD = 'http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd';
	const EXTENSION_NAME = 'TrackPointExtension';
	const EXTENSION_NAMESPACE_PREFIX = 'gpxtpx';

	/** @var  float */
	public $speed;

	/** @var  float */
	public $heartRate;

	/** @var  float */
	public $avgTemperature;

	/** @var  float */
	public $cadence;

	/** @var  float */
	public $course;

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
			'speed' => $this->speed,
			'heartRate' => $this->heartRate,
			'avgTemperature' => $this->avgTemperature,
			'cadence' => $this->cadence,
			'course' => $this->course
		];
	}
}