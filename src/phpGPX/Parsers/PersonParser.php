<?php

/**
 * Created            16/02/2017 23:08
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Person;

/**
 * Class PersonParser
 * @package phpGPX\Parsers
 */
abstract class PersonParser
{
	public static string $tagName = 'author';

	/**
	 * @param \SimpleXMLElement $node
	 * @return Person
	 */
	public static function parse(\SimpleXMLElement $node): Person
	{
		$person = new Person();

		$person->name = isset($node->name) ? ((string) $node->name) : null;
		$person->email = isset($node->email) ? EmailParser::parse($node->email) : null;
		$person->links = null;
		if (isset($node->link)) {
			$person->links = [];
			foreach ($node->link as $linkNode) {
				$person->links[] = LinkParser::parse($linkNode);
			}
		}

		return $person;
	}

	public static function toXML(Person $person, \DOMDocument &$document): \DOMElement
	{
		$node =  $document->createElement(self::$tagName);

		if (!empty($person->name)) {
			$child = $document->createElement('name', $person->name);
			$node->appendChild($child);
		}

		if (!empty($person->email)) {
			$child = EmailParser::toXML($person->email, $document);
			$node->appendChild($child);
		}

		if (!empty($person->links)) {
			foreach ($person->links as $link) {
				$node->appendChild(LinkParser::toXML($link, $document));
			}
		}

		return $node;
	}
}
