<?php
/**
 * Created            14/02/2017 18:17
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

/**
 * Class Link according to GPX 1.1 specification.
 * A link to an external resource (Web page, digital photo, video clip, etc) with additional information.
 * @see http://www.topografix.com/GPX/1/1/#type_linkType
 * @package phpGPX\Models
 */
class Link implements Summarizable
{

	/**
	 * URL of hyperlink.
	 * @var string
	 */
	public $href;

	/**
	 * Text of hyperlink.
	 * @var string|null
	 */
	public $text;

	/**
	 * Mime type of content (image/jpeg)
	 * @var string|null
	 */
	public $type;

	/**
	 * Link constructor.
	 */
	public function __construct()
	{
		$this->href = null;
		$this->text = null;
		$this->type = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'href' => (string) $this->href,
			'text' => $this->text,
			'type' => $this->type
		];
	}
}
