<?php
/**
 * Created            26/08/16 14:22
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;


use phpGPX\Helpers\Utils;
use phpGPX\phpGPX;

class Point implements Summarizable
{
	/**
	 * Latitude
	 * @var float
	 */
	public $latitude;

	/**
	 * Longitude
	 * @var float
	 */
	public $longitude;

	/**
	 * Altitude in meters (m)
	 * @var double
	 */
	public $altitude;

	/**
	 * Difference in in distance (in meters) between last point
	 * @var double
	 */
	public $difference;

	/**
	 * Distance from collection start in meters
	 * @var double
	 */
	public $distance;

	/**
	 * Name of point if defined
	 * @var string
	 */
	public $name;

	/** @var  Extension */
	public $extension;

	/** @var  \DateTime */
	public $timestamp;

	/**
	 * Type of the point (parent collation type (ROUTE|WAYPOINT|TRACK))
	 * @var string
	 */
	private $pointType;

	/**
	 * Point constructor.
	 * @param string $pointType
	 */
	public function __construct($pointType)
	{
		$this->pointType = $pointType;
		$this->extension = new Extension();
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function summary()
	{
		return [
			'latitude' => (float) $this->latitude,
			'longitude' => (float) $this->longitude,
			'altitude' => (double) $this->altitude,
			'difference' => (double) $this->difference,
			'distance' => (double) $this->distance,
			'name' => (string) $this->name,
			'timestamp' => Utils::formatDateTime($this->timestamp, phpGPX::$DATETIME_FORMAT, phpGPX::$DATETIME_TIMEZONE_OUTPUT),
			'extension' => $this->extension->summary()
		];
	}
}