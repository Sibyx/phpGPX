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
abstract class MetadataParser extends AbstractParser
{
	private static string $tagName = 'metadata';

	protected static function getAttributeMapper(): array
	{
		return [
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
				'type' => 'object',
				'parser' => PersonParser::class,
			],
			'copyright' => [
				'name' => 'copyright',
				'type' => 'object',
				'parser' => CopyrightParser::class,
			],
			'link' => [
				'name' => 'links',
				'type' => 'array',
				'parser' => LinkParser::class,
			],
			'time' => [
				'name' => 'time',
				'type' => 'datetime'
			],
			'keywords' => [
				'name' => 'keywords',
				'type' => 'string'
			],
			'bounds' => [
				'name' => 'bounds',
				'type' => 'object',
				'parser' => BoundsParser::class,
			],
			'extensions' => [
				'name' => 'extensions',
				'type' => 'object',
				'parser' => ExtensionParser::class,
			]
		];
	}

	/**
	 * @param \SimpleXMLElement $node
	 * @return Metadata
	 */
	public static function parse(\SimpleXMLElement $node): Metadata
	{
		$metadata = new Metadata();

		self::mapAttributesFromXML($node, $metadata);

		// Datetime
		$metadata->time = isset($node->time) ? DateTimeHelper::parseDateTime($node->time) : null;

		// Delegated parsers
		foreach (self::getAttributeMapper() as $key => $attribute) {
			if (isset($attribute['parser'])) {
				$metadata->{$attribute['name']} = self::parseDelegated($node, $key, $attribute);
			}
		}

		return $metadata;
	}

	/**
	 * @param Metadata $metadata
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Metadata $metadata, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement(self::$tagName);

		self::mapAttributesToXML($metadata, $document, $node);

		// Datetime
		if ($metadata->time !== null) {
			$child = $document->createElement('time', DateTimeHelper::formatDateTime($metadata->time));
			$node->appendChild($child);
		}

		// Delegated parsers
		foreach (self::getAttributeMapper() as $key => $attribute) {
			if (isset($attribute['parser'])) {
				self::serializeDelegated($metadata->{$attribute['name']}, $attribute, $document, $node);
			}
		}

		return $node;
	}
}