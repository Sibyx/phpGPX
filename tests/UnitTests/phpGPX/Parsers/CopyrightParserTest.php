<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Tests\UnitTests\phpGPX\Parsers;

use phpGPX\Models\Copyright;
use phpGPX\Parsers\CopyrightParser;
use UnitTests\phpGPX\Parsers\AbstractParserTest;

class CopyrightParserTest extends AbstractParserTest
{
	protected $testModelClass = Copyright::class;
	protected $testParserClass = CopyrightParser::class;

	/**
	 * @var Copyright
	 */
	protected $testModelInstance;

	public static function createTestInstance()
	{
		$copyright = new Copyright();

		$copyright->author = "Jakub Dubec";
		$copyright->license = "https://github.com/Sibyx/phpGPX/blob/master/LICENSE";
		$copyright->year = '2017';

		return $copyright;
	}

	protected function setUp()
	{
		parent::setUp();

		$this->testModelInstance = self::createTestInstance();
	}

	public function testParse()
	{
		$copyright = CopyrightParser::parse($this->testXmlFile->copyright);

		$this->assertNotEmpty($copyright);

		$this->assertEquals($this->testModelInstance->author, $copyright->author);
		$this->assertEquals($this->testModelInstance->license, $copyright->license);
		$this->assertEquals($this->testModelInstance->year, $copyright->year);

		$this->assertEquals($this->testModelInstance->toArray(), $copyright->toArray());
	}

	/**
	 * Returns output of ::toXML method of tested parser.
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	protected function convertToXML(\DOMDocument $document)
	{
		return CopyrightParser::toXML($this->testModelInstance, $document);
	}
}
