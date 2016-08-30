<?php
/**
 * Created            26/08/16 14:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


class Collection
{

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $source;

	/**
	 * @var \DateTime
	 */
	public $startedAt;

	/**
	 * @var \DateTime
	 */
	public $finishedAt;

	/**
	 * @var Segment[]
	 */
	public $segments = [];

	/**
	 * @var Stats
	 */
	public $stats;

	/**
	 * @var Point
	 */
	public $startingPoint;

	/**
	 * Collection constructor.
	 */
	public function __construct()
	{
		$this->stats = new Stats();
	}

}