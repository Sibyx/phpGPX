<?php

namespace phpGPX\Parsers;

/**
 * Class AbstractParser
 * Base class for parsers that use an attribute mapper array.
 *
 * Attribute mapper format:
 *   'xmlElementName' => [
 *       'name' => 'modelPropertyName',
 *       'type' => 'string|int|integer|float|object|array|datetime',
 *       'parser' => ParserClass::class,     // optional, for delegated parsing
 *       'xmlAttribute' => true,             // optional, read from XML attribute instead of child element
 *   ]
 *
 * Parser contract: every parser's parse() accepts a single SimpleXMLElement node
 * and returns a single model object (or null). Iteration over collections is handled
 * here in parseDelegated/serializeDelegated based on the 'type' key.
 *
 * @package phpGPX\Parsers
 */
abstract class AbstractParser
{
	/**
	 * @return array
	 */
	abstract protected static function getAttributeMapper(): array;

	/**
	 * Map scalar attributes from an XML node onto a model object.
	 * Skips entries with 'parser', 'datetime', 'object', or 'array' types.
	 *
	 * @param \SimpleXMLElement $node
	 * @param object $model
	 */
	protected static function mapAttributesFromXML(\SimpleXMLElement $node, object $model): void
	{
		foreach (static::getAttributeMapper() as $xmlKey => $attribute) {
			if (isset($attribute['parser']) || in_array($attribute['type'], ['object', 'array', 'datetime'])) {
				continue;
			}

			if (!empty($attribute['xmlAttribute'])) {
				if (isset($node[$xmlKey])) {
					$value = (string) $node[$xmlKey];
					settype($value, $attribute['type']);
					$model->{$attribute['name']} = $value;
				}
			} else {
				if (isset($node->$xmlKey)) {
					$value = (string) $node->$xmlKey;
					settype($value, $attribute['type']);
					$model->{$attribute['name']} = $value;
				}
			}
		}
	}

	/**
	 * Map scalar model properties onto XML elements.
	 * Skips entries with 'parser', 'datetime', 'object', or 'array' types.
	 *
	 * @param object $model
	 * @param \DOMDocument $document
	 * @param \DOMElement $node
	 */
	protected static function mapAttributesToXML(object $model, \DOMDocument &$document, \DOMElement $node): void
	{
		foreach (static::getAttributeMapper() as $xmlKey => $attribute) {
			$value = $model->{$attribute['name']} ?? null;
			if ($value === null) {
				continue;
			}

			if (isset($attribute['parser']) || in_array($attribute['type'], ['object', 'array', 'datetime'])) {
				continue;
			}

			if (!empty($attribute['xmlAttribute'])) {
				$node->setAttribute($xmlKey, (string) $value);
			} else {
				$child = $document->createElement($xmlKey);
				$child->appendChild($document->createTextNode((string) $value));
				$node->appendChild($child);
			}
		}
	}

	/**
	 * Parse a delegated child using the parser class from the attribute mapper.
	 *
	 * For 'object' types: calls parser::parse() once, returns the model or null.
	 * For 'array' types: iterates child elements, calls parser::parse() per element,
	 * collects non-null results into an array.
	 *
	 * @param \SimpleXMLElement $parentNode
	 * @param string $xmlKey
	 * @param array $attribute
	 * @return mixed
	 */
	protected static function parseDelegated(\SimpleXMLElement $parentNode, string $xmlKey, array $attribute): mixed
	{
		if (!isset($parentNode->$xmlKey)) {
			return $attribute['type'] === 'array' ? [] : null;
		}

		$parserClass = $attribute['parser'];

		if ($attribute['type'] === 'array') {
			$items = [];
			foreach ($parentNode->$xmlKey as $childNode) {
				$item = $parserClass::parse($childNode);
				if ($item !== null) {
					$items[] = $item;
				}
			}
			return $items;
		}

		return $parserClass::parse($parentNode->$xmlKey);
	}

	/**
	 * Serialize a delegated property to XML using the parser class from the attribute mapper.
	 *
	 * For 'object' types: calls parser::toXML() once, appends the element.
	 * For 'array' types: iterates items, calls parser::toXML() per item, appends each.
	 *
	 * @param mixed $value
	 * @param array $attribute
	 * @param \DOMDocument $document
	 * @param \DOMElement $parentNode
	 */
	protected static function serializeDelegated(mixed $value, array $attribute, \DOMDocument &$document, \DOMElement $parentNode): void
	{
		if ($value === null || (is_array($value) && empty($value))) {
			return;
		}

		$parserClass = $attribute['parser'];

		if ($attribute['type'] === 'array') {
			foreach ($value as $item) {
				$parentNode->appendChild($parserClass::toXML($item, $document));
			}
		} else {
			$parentNode->appendChild($parserClass::toXML($value, $document));
		}
	}
}