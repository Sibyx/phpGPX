<?php
/**
 * Created            15/02/2017 19:00
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;


use phpGPX\Helpers\SerializationHelper;
use phpGPX\Models\Extensions\TrackPointExtension;

/**
 * Class Extensions
 * TODO: http://www.garmin.com/xmlschemas/GpxExtensions/v3
 * @package phpGPX\Models
 */
class Extensions implements Summarizable
{

	/**
	 * @var TrackPointExtension
	 */
	public $trackPointExtension;

	/**
	 * Extensions constructor.
	 */
	public function __construct()
	{
		$this->trackPointExtension = null;
	}

	/**
	 * Serialize object to array
	 * @return array
	 */
	function toArray()
	{
		return [
			'trackpoint' => SerializationHelper::serialize($this->trackPointExtension)
		];
	}
}