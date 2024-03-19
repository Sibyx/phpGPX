<?php
/**
 * @author miqwit
 */

namespace phpGPX\Tests\UnitTests\phpGPX\Parsers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Point;
use phpGPX\Parsers\SegmentParser;
use phpGPX\Parsers\PointParser;
use phpGPX\phpGPX;
use UnitTests\phpGPX\Parsers\AbstractParserTest;

class PointParserTest extends AbstractParserTest
{
	protected $testModelClass = Point::class;
	protected $testParserClass = PointParser::class;

	/**
	 * @var Point
	 */
	protected $testModelInstance;

	public static function createTestInstance() : Point
	{
		$point = new Point(Point::TRACKPOINT);
		$point->latitude = 46.571948;
		$point->longitude = 8.414757;
		$point->elevation = 2419;
		$point->time = DateTimeHelper::parseDateTime("2017-08-13T07:10:41.000Z");

		return $point;
	}

	public static function createTestInstanceWithValues(
		float $latitude,
		float $longitude,
		float $elevation,
		string $timeAsString) : Point
	{
		$point = new Point(Point::TRACKPOINT);
		$point->latitude = $latitude;
		$point->longitude = $longitude;
		$point->elevation = $elevation;
		$point->time = DateTimeHelper::parseDateTime($timeAsString);

		return $point;
	}

	protected function setUp(): void
    {
		parent::setUp();

		$this->testModelInstance = self::createTestInstance();
	}

	public function testParse()
	{
		$point = PointParser::parse($this->testXmlFile->trkpt);

		$this->assertNotEmpty($point);

		// Primitive attributes
		$this->assertEquals($this->testModelInstance->latitude, $point->latitude);
		$this->assertEquals($this->testModelInstance->longitude, $point->longitude);
		$this->assertEquals($this->testModelInstance->elevation, $point->elevation);
		$this->assertEquals($this->testModelInstance->time, $point->time);
	}

	/**
	 * Returns output of ::toXML method of tested parser.
	 * @depends testParse
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	protected function convertToXML(\DOMDocument $document)
	{
		return PointParser::toXML($this->testModelInstance, $document);
	}
}
