<?php
/**
 * Created            16/02/2017 16:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models\Extensions;

use phpGPX\GpxSerializable;

abstract class AbstractExtension implements \JsonSerializable, GpxSerializable
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

	/**
	 * GPX serializer
	 * @param \SimpleXMLElement $node
	 * @return void
	 */
	public static function gpxSerialize(\SimpleXMLElement $node): void
	{
		// Implementation required by GpxSerializable interface
	}

	/**
	 * GPX deserializer
	 * @param \DOMDocument $document
	 * @return void
	 */
	public function gpxDeserialize(\DOMDocument &$document): void
	{
		// Implementation required by GpxSerializable interface
	}
}
