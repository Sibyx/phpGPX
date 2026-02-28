<?php
/**
 * Created            15/02/2017 19:00
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\SerializationHelper;
use phpGPX\Models\Extensions\StyleExtension;
use phpGPX\Models\Extensions\TrackPointExtension;

/**
 * Class Extensions
 * TODO: http://www.garmin.com/xmlschemas/GpxExtensions/v3
 * @package phpGPX\Models
 */
class Extensions implements Summarizable
{
	/**
	 * GPX Garmin TrackPointExtension v1
	 * @see 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1'
	 * @var TrackPointExtension
	 */
	public $trackPointExtension;

	/**
	 * GPX Style Extension v2
	 * @see 'http://www.topografix.com/GPX/gpx_style/0/2/'
	 * @var StyleExtension
	 */
	public $styleExtension;

	/**
	 * @var []
	 */
	public $unsupported = [];

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
				'trackpoint' => SerializationHelper::serialize($this->trackPointExtension),
				'line' => SerializationHelper::serialize($this->styleExtension),
				'unsupported' => $this->unsupported,
			];
	}
}
