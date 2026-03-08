<?php

namespace phpGPX\Tests\Unit\Helpers;

use phpGPX\Helpers\SerializationHelper;
use PHPUnit\Framework\TestCase;

class SerializationHelperTest extends TestCase
{
	public function testPositionWithElevation(): void
	{
		$pos = SerializationHelper::position(9.860, 54.932, 100.5);
		$this->assertEquals([9.860, 54.932, 100.5], $pos);
	}

	public function testPositionWithoutElevation(): void
	{
		$pos = SerializationHelper::position(9.860, 54.932);
		$this->assertEquals([9.860, 54.932], $pos);
	}

	public function testPositionWithNullElevation(): void
	{
		$pos = SerializationHelper::position(9.860, 54.932, null);
		$this->assertEquals([9.860, 54.932], $pos);
	}
}