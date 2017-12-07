<?php
/**
 * Created            17/02/2017 15:58
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Metadata;

/**
 * Class MetadataParser
 * @package phpGPX\Parsers
 */
abstract class MetadataParser
{
	private static $tagName = 'metadata';

	private static $attributeMapper = [
		'name' => [
			'name' => 'name',
			'type' => 'string'
		],
		'desc' => [
			'name' => 'description',
			'type' => 'string'
		],
		'author' => [
			'name' => 'author',
			'type' => 'object'
		],
		'copyright' => [
			'name' => 'copyright',
			'type' => 'object'
		],
		'link' => [
			'name' => 'links',
			'type' => 'array'
		],
		'time' => [
			'name' => 'time',
			'type' => 'object'
		],
		'keywords' => [
			'name' => 'keywords',
			'type' => 'string'
		],
		'bounds' => [
			'name' => 'bounds',
			'type' => 'object'
		],
		'extensions' => [
			'name' => 'extensions',
			'type' => 'object'
		]
	];

	/**
	 * @param \SimpleXMLElement $node
	 * @return Metadata
	 */
	public static function parse(\SimpleXMLElement $node)
	{
		$metadata = new Metadata();

		foreach (self::$attributeMapper as $key => $attribute) {
			switch ($key) {
				case 'author':
					$metadata->author = isset($node->author) ? PersonParser::parse($node->author) : null;
					break;
				case 'copyright':
					$metadata->copyright = isset($node->copyright) ? CopyrightParser::parse($node->copyright) : null;
					break;
				case 'link':
					$metadata->links = isset($node->link) ? LinkParser::parse($node->link) : null;
					break;
				case 'time':
					$metadata->time = isset($node->time) ? DateTimeHelper::parseDateTime($node->time) : null;
					break;
				case 'bounds':
					$metadata->bounds = isset($node->bounds) ? BoundsParser::parse($node->bounds) : null;
					break;
				case 'extensions':
					$metadata->extensions = isset($node->extensions) ? ExtensionParser::parse($node->extensions) : null;
					break;
				default:
					if (!in_array($attribute['type'], ['object', 'array'])) {
						$metadata->{$attribute['name']} = isset($node->$key) ? $node->$key : null;
						if (!is_null($metadata->{$attribute['name']})) {
							settype($metadata->{$attribute['name']}, $attribute['type']);
						}
					}
					break;
			}
		}

		return $metadata;
	}

	public static function toXML(Metadata $metadata, \DOMDocument &$document)
	{
		$node =  $document->createElement(self::$tagName);

		foreach (self::$attributeMapper as $key => $attribute) {
			if (!is_null($metadata->{$attribute['name']})) {
				switch ($key) {
					case 'author':
						$child = PersonParser::toXML($metadata->author, $document);
						break;
					case 'copyright':
						$child = CopyrightParser::toXML($metadata->copyright, $document);
						break;
					case 'link':
						$child = LinkParser::toXMLArray($metadata->links, $document);
						break;
					case 'time':
						$child = $document->createElement('time', DateTimeHelper::formatDateTime($metadata->time));
						break;
					case 'bounds':
						$child = BoundsParser::toXML($metadata->bounds, $document);
						break;
					case 'extensions':
						$child = ExtensionParser::toXML($metadata->extensions, $document);
						break;
					default:
						$child = $document->createElement($key);
						$elementText = $document->createTextNode((string) $metadata->{$attribute['name']});
						$child->appendChild($elementText);
						break;
				}

				if (is_array($child)) {
					foreach ($child as $item) {
						$node->appendChild($item);
					}
				} else {
					$node->appendChild($child);
				}
			}
		}

		return $node;
	}
}
