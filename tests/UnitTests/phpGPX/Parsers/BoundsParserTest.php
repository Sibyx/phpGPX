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
		return new Bounds(49.072489, 18.814543, 49.090543, 18.886939);
	}

	protected function setUp(): void
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
