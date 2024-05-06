<?php
/**
 * @author miqwit
 */

namespace phpGPX\Tests\UnitTests\phpGPX\Parsers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Segment;
use phpGPX\Parsers\SegmentParser;
use phpGPX\phpGPX;
use UnitTests\phpGPX\Parsers\AbstractParserTest;

class SegmentParserTest extends AbstractParserTest
{
	protected $testModelClass = Segment::class;
	protected $testParserClass = SegmentParser::class;

	/**
	 * @var Segment
	 */
	protected $testModelInstance;

	public static function createTestInstance()
	{
		$segment = new Segment();
		$segment->points = [
			PointParserTest::createTestInstanceWithValues(46.571948, 8.414757, 2419, "2017-08-13T07:10:41.000Z"),
			PointParserTest::createTestInstanceWithValues(46.572016, 8.414866, 2418.8833883882, "2017-08-13T07:10:54.000Z"),
			PointParserTest::createTestInstanceWithValues(46.572088, 8.414911, 2419.8999900064, "2017-08-13T07:11:56.000Z"),
			PointParserTest::createTestInstanceWithValues(46.572069, 8.414912, 2422, "2017-08-13T07:12:15.000Z"),
			PointParserTest::createTestInstanceWithValues(46.572054, 8.414888, 2425, "2017-08-13T07:12:18.000Z")
		];
		$segment->recalculateStats();

		return $segment;
	}

	protected function setUp(): void
    {
		parent::setUp();

		$this->testModelInstance = self::createTestInstance();
	}

	public function testParse()
	{
		$segment = SegmentParser::parse($this->testXmlFile->trkseg);

		$this->assertNotEmpty($segment);

		// Test second point
		$point = $segment[0]->points[1];
		$this->assertEquals($this->testModelInstance->points[1]->latitude, $point->latitude);
		$this->assertEquals($this->testModelInstance->points[1]->longitude, $point->longitude);
		$this->assertEquals($this->testModelInstance->points[1]->elevation, $point->elevation);
		$this->assertEquals($this->testModelInstance->points[1]->time, $point->time);

		// Stats
		$this->assertNotEmpty($this->testModelInstance->stats);

		// Check the boundaries
		$nw = $this->testModelInstance->stats->bounds[0];
		$se = $this->testModelInstance->stats->bounds[1];
		$this->assertEquals(46.572088, $nw["lat"]);
		$this->assertEquals(8.414757, $nw["lng"]);
		$this->assertEquals(46.571948, $se["lat"]);
		$this->assertEquals(8.414912, $se["lng"]);
	}

	/**
	 * Returns output of ::toXML method of tested parser.
	 * @depends testParse
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	protected function convertToXML(\DOMDocument $document)
	{
		return SegmentParser::toXML($this->testModelInstance, $document);
	}
}
