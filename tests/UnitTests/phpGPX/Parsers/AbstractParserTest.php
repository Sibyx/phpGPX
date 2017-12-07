<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace UnitTests\phpGPX\Parsers;

use phpGPX\Models\Summarizable;
use PHPUnit\Framework\TestCase;

abstract class AbstractParserTest extends TestCase
{
	/**
	 * @var \SimpleXMLElement
	 */
	protected $testXmlFile;

	/**
	 * Instance of model holding data for parser.
	 * EXAMPLE: model phpGPX\Models\Bounds belongs to parser phpGPX\Parsers\BoundsParser
	 * @var Summarizable
	 */
	protected $testModelInstance;

	/**
	 * Full name with namespace for models class.
	 * EXAMPLE: phpGPX\Models\Bounds
	 * @var string
	 */
	protected $testModelClass;

	/**
	 * Full name with namespace for parser class.
	 * EXAMPLE: phpGPX\Parsers\BoundsParser
	 * @var string
	 */
	protected $testParserClass;

	protected function setUp()
	{
		$reflection = new \ReflectionClass($this->testParserClass);

		$this->testXmlFile = simplexml_load_file(sprintf("%s/%sTest.xml", __DIR__, $reflection->getShortName()));
	}

	abstract public function testParse();

	/**
	 * Returns output of ::toXML method of tested parser.
	 * @depends testParse
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	abstract protected function convertToXML(\DOMDocument $document);

	public function testToXML()
	{
		$document = new \DOMDocument("1.0", 'UTF-8');

		$root = $document->createElement("document");
		$root->appendChild($this->convertToXML($document));

		$document->appendChild($root);

		$this->assertXmlStringEqualsXmlString($this->testXmlFile->asXML(), $document->saveXML());
	}

	public function testToJSON()
	{
		$reflection = new \ReflectionClass($this->testParserClass);

		$this->assertJsonStringEqualsJsonFile(
			sprintf("%s/%sTest.json", __DIR__, $reflection->getShortName()),
			json_encode($this->testModelInstance->toArray())
		);
	}
}
