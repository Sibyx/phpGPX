<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace UnitTests\phpGPX\Helpers;

use phpGPX\Helpers\DateTimeHelper;
use phpGPX\Models\Point;
use PHPUnit\Framework\TestCase;

class DateTimeHelperTest extends TestCase
{
	public function testComparePointsByTimestamp()
	{
		// 2017-08-12T20:16:29+00:00
		$point1 = new Point(Point::WAYPOINT);
		$time1 = new \DateTime("2017-08-12T20:16:29+00:00", new \DateTimeZone("UTC"));
		$point1->time = $time1;

		// 2017-08-12T20:15:19+00:00
		$point2 = new Point(Point::WAYPOINT);
		$time2 = new \DateTime("2017-08-12T20:15:19+00:00", new \DateTimeZone("UTC"));
		$point2->time = $time2;

		$this->assertTrue(($time1 > $time2) && DateTimeHelper::comparePointsByTimestamp($point1, $point2));
	}

	public function testFormatDateTime()
	{
		// 1. Basic test
		$datetime = new \DateTime("2017-08-12T20:16:29+00:00");

		$this->assertEquals(
			$datetime->format("Y-m-d H:i:s"),
			DateTimeHelper::formatDateTime($datetime, "Y-m-d H:i:s")
		);

		// 2. NULL value
		$datetime = null;

		$this->assertNull(DateTimeHelper::formatDateTime($datetime), "NULL input");

		// 3. Empty string
		$datetime = "";

		$this->assertNull(DateTimeHelper::formatDateTime($datetime), "Empty string input");

		// 4. Timezone
		$datetime = new \DateTime("2017-08-12T20:16:29+00:00");

		$this->assertEquals(
			"2017-08-12 21:16:29",
			DateTimeHelper::formatDateTime($datetime, "Y-m-d H:i:s", '+01:00')
		);
	}

	public function testParseDateTime()
	{
		// 1. Valid string
		$this->assertEquals(
			new \DateTime("2017-08-12T20:16:29+00:00"),
			DateTimeHelper::parseDateTime("2017-08-12T20:16:29+00:00")
		);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testParseDateTimeInvalidInput()
	{
		DateTimeHelper::parseDateTime("Invalid exception");
	}
}
