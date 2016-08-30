<?php
/**
 * Created            26/08/16 14:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


class Collation
{

	/** @var  string */
	public $name;

	/** @var  string */
	public $type;

	/** @var  string */
	public $url;

	/** @var  string */
	public $source;

	/** @var  Segment[] */
	public $segments = [];

}