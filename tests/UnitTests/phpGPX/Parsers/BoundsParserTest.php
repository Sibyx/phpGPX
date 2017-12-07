<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace UnitTests\phpGPX\Parsers;

use phpGPX\Models\Bounds;
use phpGPX\Parsers\BoundsParser;

class BoundsParserTest extends AbstractParserTest
{
	protected $testModelClass = Bounds::class;
	protected $testParserClass = BoundsParser::class;

	/**
	 * @var Bounds
	 */
	protected $testModelInstance;

	public static function createTestInstance()
	{
		$bounds = new Bounds();

		$bounds->maxLatitude = 49.090543;
		$bounds->maxLongitude = 18.886939;
		$bounds->minLatitude = 49.072489;
		$bounds->minLongitude = 18.814543;

		return $bounds;
	}

	protected function setUp()
	{
		parent::setUp();

		$this->testModelInstance = self::createTestInstance();
	}

	public function testParse()
	{
		$bounds = BoundsParser::parse($this->testXmlFile->bounds);

		$this->assertNotEmpty($bounds);

		$this->assertEquals($this->testModelInstance->maxLatitude, $bounds->maxLatitude);
		$this->assertEquals($this->testModelInstance->maxLongitude, $bounds->maxLongitude);
		$this->assertEquals($this->testModelInstance->minLatitude, $bounds->minLatitude);
		$this->assertEquals($this->testModelInstance->minLongitude, $bounds->minLongitude);

		$this->assertEquals($this->testModelInstance->toArray(), $bounds->toArray());
	}

	protected function convertToXML(\DOMDocument $document)
	{
		return BoundsParser::toXML($this->testModelInstance, $document);
	}
}
