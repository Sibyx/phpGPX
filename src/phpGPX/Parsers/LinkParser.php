<?php
/**
 * Created            15/02/2017 18:44
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Link;

abstract class LinkParser
{
	private static string $tagName = 'link';

	/**
	 * Parse a single link node.
	 *
	 * @param \SimpleXMLElement $node
	 * @return Link
	 */
	public static function parse(\SimpleXMLElement $node): Link
	{
		$link = new Link();
		$link->href = isset($node['href']) ? (string) $node['href'] : null;
		$link->text = isset($node->text) ? (string) $node->text : null;
		$link->type = isset($node->type) ? (string) $node->type : null;

		return $link;
	}

	/**
	 * @param Link $link
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Link $link, \DOMDocument &$document): \DOMElement
	{
		$node = $document->createElement(self::$tagName);

		if ($link->href !== null && $link->href !== '') {
			$node->setAttribute('href', $link->href);
		}

		if ($link->text !== null && $link->text !== '') {
			$child = $document->createElement('text', $link->text);
			$node->appendChild($child);
		}

		if ($link->type !== null && $link->type !== '') {
			$child = $document->createElement('type', $link->type);
			$node->appendChild($child);
		}

		return $node;
	}
}