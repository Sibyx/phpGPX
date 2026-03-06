<?php

namespace phpGPX\Tests\Unit\Helpers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Point;
use PHPUnit\Framework\TestCase;

class DateTimeHelperTest extends TestCase
{
	public function testComparePointsByTimestamp(): void
	{
		$point1 = new Point(Point::WAYPOINT);
		$time1 = new \DateTime("2017-08-12T20:16:29+00:00", new \DateTimeZone("UTC"));
		$point1->time = $time1;

		$point2 = new Point(Point::WAYPOINT);
		$time2 = new \DateTime("2017-08-12T20:15:19+00:00", new \DateTimeZone("UTC"));
		$point2->time = $time2;

		$this->assertTrue(($time1 > $time2) && DateTimeHelper::comparePointsByTimestamp($point1, $point2));
	}

	public function testFormatDateTime(): void
	{
		$datetime = new \DateTime("2017-08-12T20:16:29+00:00");

		$this->assertEquals(
			$datetime->format("Y-m-d H:i:s"),
			DateTimeHelper::formatDateTime($datetime, "Y-m-d H:i:s")
		);

		$this->assertNull(DateTimeHelper::formatDateTime(null), "NULL input");
		$this->assertNull(DateTimeHelper::formatDateTime(""), "Empty string input");

		$datetime = new \DateTime("2017-08-12T20:16:29+00:00");
		$this->assertEquals(
			"2017-08-12 21:16:29",
			DateTimeHelper::formatDateTime($datetime, "Y-m-d H:i:s", '+01:00')
		);
	}

	public function testParseDateTime(): void
	{
		$this->assertEquals(
			new \DateTime("2017-08-12T20:16:29+00:00"),
			DateTimeHelper::parseDateTime("2017-08-12T20:16:29+00:00")
		);
	}

	public function testParseDateTimeInvalidInput(): void
	{
		$this->expectException("Exception");
		DateTimeHelper::parseDateTime("Invalid exception");
	}
}