<?php
/**
 * Created            16/02/2017 23:02
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Email;

/**
 * Class EmailParser
 * @package phpGPX\Parsers
 */
abstract class EmailParser
{
	private static $tagName = 'email';

	/**
	 * @param \SimpleXMLElement $node
	 * @return Email
	 */
	public static function parse(\SimpleXMLElement $node)
	{
		$email = new Email();

		$email->id = isset($node['id']) ? (string) $node['id'] : null;
		$email->domain = isset($node['domain']) ? (string) $node['domain'] : null;

		return $email;
	}


	/**
	 * @param Email $email
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	public static function toXML(Email $email, \DOMDocument &$document)
	{
		$node =  $document->createElement(self::$tagName);

		if (!empty($email->id)) {
			$node->setAttribute('id', $email->id);
		}

		if (!empty($email->domain)) {
			$node->setAttribute('domain', $email->domain);
		}

		return $node;
	}
}
