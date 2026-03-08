<?php
/**
 * Created            16/02/2017 16:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models\Extensions;

abstract class AbstractExtension implements \JsonSerializable
{

	/**
	 * XML namespace of extension
	 * @var string
	 */
	public string $namespace;

	/**
	 * Node name extension.
	 * @var string
	 */
	public string $extensionName;

	/**
	 * AbstractExtension constructor.
	 * @param string $namespace
	 * @param string $extensionName
	 */
	public function __construct(string $namespace, string $extensionName)
	{
		$this->namespace = $namespace;
		$this->extensionName = $extensionName;
	}
}
