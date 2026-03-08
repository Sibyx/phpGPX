<?php
/**
 * Created            27/02/26 23:09
 * @author            Peter Newman
 */

namespace phpGPX\Models\Extensions;

use phpGPX\Helpers\SerializationHelper;

/**
 * Class StyleExtension
 * Extension version: v2
 * Based on namespace: http://www.topografix.com/GPX/gpx_style/0/2/gpx_style.xsd
 * @package phpGPX\Models\Extensions
 */
class StyleExtension extends AbstractExtension
{
	const EXTENSION_NAMESPACE = 'http://www.topografix.com/GPX/gpx_style/0/2';
	const EXTENSION_NAMESPACE_XSD = 'http://www.topografix.com/GPX/gpx_style/0/2/gpx_style.xsd';

	const EXTENSION_NAME = 'line';
	const EXTENSION_NAMESPACE_PREFIX = 'gpxstyle';

	/**
	 * @var string
	 */
	public $color;

	/**
	 * @var float
	 */
	public $opacity;

	/**
	 * @var float
	 */
	public $width;

	/**
	 * @var string
	 */
	public $pattern;

	/**
	 * @var string
	 */
	public $linecap;

	/**
	 * StyleExtension constructor.
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
			'color' => SerializationHelper::stringOrNull($this->color),
			'opacity' => SerializationHelper::floatOrNull($this->opacity),
			'width' => SerializationHelper::floatOrNull($this->width),
			// 'pattern' => SerializationHelper::stringOrNull($this->pattern),
			'linecap' => SerializationHelper::stringOrNull($this->linecap),
			// 'dasharray' => SerializationHelper::stringOrNull($this->dasharray),
			// 'extensions' => SerializationHelper::stringOrNull($this->extensions),
		];
	}
}
