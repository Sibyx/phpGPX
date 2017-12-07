<?php
/**
 * Created            13/09/16 10:22
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Helpers\SerializationHelper;

/**
 * Class Metadata
 * Information about the GPX file, author, and copyright restrictions goes in the metadata section.
 * Providing rich, meaningful information about your GPX files allows others to search for and use your GPS data.
 * @package phpGPX\Models
 */
class Metadata implements Summarizable
{

	/**
	 * The name of the GPX file.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public $name;

	/**
	 * A description of the contents of the GPX file.
	 * Original GPX 1.1 attribute.
	 * @var string|null
	 */
	public $description;

	/**
	 * The person or organization who created the GPX file.
	 * An original GPX 1.1 attribute.
	 * @var Person|null
	 */
	public $author;

	/**
	 * Copyright and license information governing use of the file.
	 * Original GPX 1.1 attribute.
	 * @var Copyright|null
	 */
	public $copyright;

	/**
	 * Original GPX 1.1 attribute.
	 * @var Link[]|null
	 */
	public $links;

	/**
	 * Date of GPX creation
	 * @var \DateTime
	 */
	public $time;

	/**
	 * Keywords associated with the file. Search engines or databases can use this information to classify the data.
	 * @var string|null
	 */
	public $keywords;

	/**
	 * Minimum and maximum coordinates which describe the extent of the coordinates in the file.
	 * Original GPX 1.1 attribute.
	 * @var Bounds|null
	 */
	public $bounds;

	/**
	 * Extensions.
	 * @var Extensions|null
	 */
	public $extensions;

	/**
	 * Metadata constructor.
	 */
	public function __construct()
	{
		$this->name = null;
		$this->description = null;
		$this->author = null;
		$this->copyright = null;
		$this->links = [];
		$this->time = null;
		$this->keywords = null;
		$this->bounds = null;
		$this->extensions = null;
	}


	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray()
	{
		return [
			'name' => SerializationHelper::stringOrNull($this->name),
			'desc' => SerializationHelper::stringOrNull($this->description),
			'author' => SerializationHelper::serialize($this->author),
			'copyright' => SerializationHelper::serialize($this->copyright),
			'links' => SerializationHelper::serialize($this->links),
			'time' => DateTimeHelper::formatDateTime($this->time),
			'keywords' => SerializationHelper::stringOrNull($this->keywords),
			'bounds' => SerializationHelper::serialize($this->bounds),
			'extensions' => SerializationHelper::serialize($this->extensions)
		];
	}
}
