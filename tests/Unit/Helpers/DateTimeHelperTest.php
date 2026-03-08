<?php

namespace phpGPX\Tests\Unit\Helpers;

use phpGPX\Helpers\DateTimeHelper;
use PHPUnit\Framework\TestCase;

class DateTimeHelperTest extends TestCase
{
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