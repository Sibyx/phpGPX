<?php
/**
 * Created            26/08/16 17:05
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


class Extension
{

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