<?php
/**
 * Created            15/02/2017 19:00
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Models\Extensions\TrackPointExtension;

/**
 * Class Extensions
 * TODO: http://www.garmin.com/xmlschemas/GpxExtensions/v3
 * @package phpGPX\Models
 */
class Extensions implements \JsonSerializable
{
	/**
	 * GPX Garmin TrackPointExtension v1
	 * @see 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1'
	 * @var TrackPointExtension|null
	 */
	public ?TrackPointExtension $trackPointExtension;

	/**
	 * @var array
	 */
	public array $unsupported = [];

	/**
	 * Extensions constructor.
	 */
	public function __construct()
	{
		$this->trackPointExtension = null;
	}

	public function jsonSerialize(): array
	{
		return array_filter([
			'trackpoint' => $this->trackPointExtension,
			'unsupported' => !empty($this->unsupported) ? $this->unsupported : null,
		], fn($v) => $v !== null);
	}
}
