<?php
/**
 * Created            16/02/2017 16:32
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers\Extensions;


use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Parsers\ExtensionParser;

class TrackPointExtensionParser
{
	private static $attributeMapper = [
		'atemp' => [
			'name' => 'avgTemperature',
			'type' => 'float'
		],
		'hr' => [
			'name' => 'heartRate',
			'type' => 'float'
		],
		'cad' => [
			'name' => 'cadence',
			'type' => 'float'
		]
	];

	/**
	 * @param \SimpleXMLElement $node
	 * @return TrackPointExtension
	 */
	public static function parse($node)
	{
		$extension = new TrackPointExtension();

		foreach (self::$attributeMapper as $key => $attribute)
		{
			$extension->{$attribute['name']} = isset($node->$key) ? $node->$key : null;
			if (!is_null($extension->{$attribute['name']}))
			{
				settype($extension->{$attribute['name']}, $attribute['type']);
			}
		}

		return $extension;
	}

	/**
	 * @param TrackPointExtension $extension
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(TrackPointExtension $extension, \DOMDocument &$document)
	{
		$node =  $document->createElement("gpxtpx:TrackPointExtension");

		ExtensionParser::$usedNamespaces[TrackPointExtension::EXTENSION_NAME] = [
			'namespace' => TrackPointExtension::EXTENSION_NAMESPACE,
			'xsd' => TrackPointExtension::EXTENSION_NAMESPACE_XSD,
			'name' => TrackPointExtension::EXTENSION_NAME,
			'prefix' => TrackPointExtension::EXTENSION_NAMESPACE_PREFIX
		];

		foreach (self::$attributeMapper as $key => $attribute)
		{
			if (!is_null($extension->{$attribute['name']}))
			{
				$child = $document->createElement(
					sprintf("%s:%s", TrackPointExtension::EXTENSION_NAMESPACE_PREFIX, $key),
					$extension->{$attribute['name']}
				);
				$node->appendChild($child);
			}
		}

		return $node;
	}
}