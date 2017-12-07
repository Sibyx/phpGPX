<?php
/**
 * Created            26/08/16 14:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

/**
 * Class Collection
 * @package phpGPX\Models
 */
abstract class Collection implements Summarizable, StatsCalculator
{

	/**
	 * GPS name of route / track.
	 * An original GPX 1.1 attribute.
	 * @var string|null
	 */
	public $name;

	/**
	 * GPS comment for route.
	 * An original GPX 1.1 attribute.
	 * @var string|null
	 */
	public $comment;

	/**
	 * Text description of route/track for user. Not sent to GPS.
	 * An original GPX 1.1 attribute.
	 * @var string|null
	 */
	public $description;

	/**
	 * Source of data. Included to give user some idea of reliability and accuracy of data.
	 * An original GPX 1.1 attribute.
	 * @var string|null
	 */
	public $source;

	/**
	 * Links to external information about the route/track.
	 * An original GPX 1.1 attribute.
	 * @var Link[]
	 */
	public $links;

	/**
	 * GPS route/track number.
	 * An original GPX 1.1 attribute.
	 * @var int|null
	 */
	public $number;

	/**
	 * Type (classification) of route/track.
	 * An original GPX 1.1 attribute.
	 * @var string|null
	 */
	public $type;

	/**
	 * You can add extend GPX by adding your own elements from another schema here.
	 * An original GPX 1.1 attribute.
	 * @var Extensions|null
	 */
	public $extensions;

	/**
	 * Objects contains calculated statistics for collection.
	 * @var Stats|null
	 */
	public $stats;

	/**
	 * Collection constructor.
	 */
	public function __construct()
	{
		$this->name = null;
		$this->comment = null;
		$this->description = null;
		$this->source = null;
		$this->links = [];
		$this->number = null;
		$this->type = null;
		$this->extensions = null;
	}


	/**
	 * Return all points in collection.
	 * @return Point[]
	 */
	abstract public function getPoints();
}
