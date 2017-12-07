<?php
/**
 * Created            16/02/2017 16:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models\Extensions;

use phpGPX\Models\Summarizable;

abstract class AbstractExtension implements Summarizable
{

	/**
	 * XML namespace of extension
	 * @var string
	 */
	public $namespace;

	/**
	 * Node name extension.
	 * @var string
	 */
	public $extensionName;

	/**
	 * AbstractExtension constructor.
	 * @param string $namespace
	 * @param string $extensionName
	 */
	public function __construct($namespace, $extensionName)
	{
		$this->namespace = $namespace;
		$this->extensionName = $extensionName;
	}
}
