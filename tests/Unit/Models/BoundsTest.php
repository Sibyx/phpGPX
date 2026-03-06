<?php

namespace phpGPX\Tests\Unit\Models;

use phpGPX\Models\Bounds;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class BoundsTest extends TestCase
{
	protected Bounds $bounds;

	protected function setUp(): void
	{
		$this->bounds = new Bounds(
			49.072489,
			18.814543,
			49.090543,
			18.886939
		);
	}

	public function testConstructor(): void
	{
		$this->assertEquals(49.072489, $this->bounds->minLatitude);
		$this->assertEquals(18.814543, $this->bounds->minLongitude);
		$this->assertEquals(49.090543, $this->bounds->maxLatitude);
		$this->assertEquals(18.886939, $this->bounds->maxLongitude);
	}

	public function testJsonSerialize(): void
	{
		$expected = [18.814543, 49.072489, 18.886939, 49.090543];
		$this->assertEquals($expected, $this->bounds->jsonSerialize());
	}

	public function testParse(): void
	{
		$xml = new SimpleXMLElement('<bounds minlat="49.072489" minlon="18.814543" maxlat="49.090543" maxlon="18.886939" />');
		$bounds = Bounds::parse($xml);

		$this->assertInstanceOf(Bounds::class, $bounds);
		$this->assertEquals(49.072489, $bounds->minLatitude);
		$this->assertEquals(18.814543, $bounds->minLongitude);
		$this->assertEquals(49.090543, $bounds->maxLatitude);
		$this->assertEquals(18.886939, $bounds->maxLongitude);
	}

	public function testParseInvalidNode(): void
	{
		$xml = new SimpleXMLElement('<invalid />');
		$bounds = Bounds::parse($xml);

		$this->assertNull($bounds);
	}
}