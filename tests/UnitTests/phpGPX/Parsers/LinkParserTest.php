<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace UnitTests\phpGPX\Parsers;

use phpGPX\Models\Link;
use phpGPX\Parsers\LinkParser;

class LinkParserTest extends AbstractParserTest
{
	protected $testModelClass = Link::class;
	protected $testParserClass = LinkParser::class;

	/**
	 * @var Link
	 */
	protected $testModelInstance;

	/**
	 * @return Link
	 */
	public static function createTestInstance()
	{
		$link = new Link();
		$link->href = "https://jakubdubec.me";
		$link->text = "Portfolio";
		$link->type = "text/html";

		return $link;
	}

	protected function setUp()
	{
		parent::setUp();

		$this->testModelInstance = self::createTestInstance();
	}

	public function testParse()
	{
		$links = LinkParser::parse($this->testXmlFile->link);

		$this->assertNotEmpty($links);

		$link = $links[0];

		$this->assertEquals($this->testModelInstance->href, $link->href);
		$this->assertEquals($this->testModelInstance->text, $link->text);
		$this->assertEquals($this->testModelInstance->type, $link->type);

		$this->assertEquals($this->testModelInstance->toArray(), $link->toArray());
	}


	/**
	 * Returns output of ::toXML method of tested parser.
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	protected function convertToXML(\DOMDocument $document)
	{
		return LinkParser::toXML($this->testModelInstance, $document);
	}
}
