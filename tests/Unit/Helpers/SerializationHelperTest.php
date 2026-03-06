<?php

namespace phpGPX\Tests\Unit\Helpers;

use phpGPX\Helpers\SerializationHelper;
use PHPUnit\Framework\TestCase;

class SerializationHelperTest extends TestCase
{
	public function testIntegerOrNull(): void
	{
		$this->assertNull(SerializationHelper::integerOrNull(""));
		$this->assertNull(SerializationHelper::integerOrNull(null));
		$this->assertNull(SerializationHelper::integerOrNull("BLA"));
		$this->assertIsInt(SerializationHelper::integerOrNull(5));
		$this->assertIsInt(SerializationHelper::integerOrNull("5"));
	}

	public function testFloatOrNull(): void
	{
		$this->assertNull(SerializationHelper::floatOrNull(""));
		$this->assertNull(SerializationHelper::floatOrNull(null));
		$this->assertNull(SerializationHelper::floatOrNull("BLA"));
		$this->assertIsFloat(SerializationHelper::floatOrNull(5.6));
		$this->assertIsFloat(SerializationHelper::floatOrNull(5));
		$this->assertIsFloat(SerializationHelper::floatOrNull("5.6"));
		$this->assertIsFloat(SerializationHelper::floatOrNull("5"));
	}

	public function testStringOrNull(): void
	{
		$this->assertNull(SerializationHelper::stringOrNull(null));
		$this->assertIsString(SerializationHelper::stringOrNull(""));
		$this->assertIsString(SerializationHelper::stringOrNull("Bla bla"));
	}

	#[\PHPUnit\Framework\Attributes\DataProvider('dataProviderFilterNotNull')]
	public function testFilterNotNull(array $expected, array $actual): void
	{
		$this->assertEquals($expected, SerializationHelper::filterNotNull($actual));
	}

	public static function dataProviderFilterNotNull(): array
	{
		return [
			'numeric 1' => [
				[],
				[null],
			],
			'numeric 2' => [
				[],
				[null, [null]],
			],
			'numeric 3' => [
				[1 => 1],
				[null, 1],
			],
			'numeric 4' => [
				[1 => 1, 3 => 2],
				[null, 1, null, 2, null],
			],
			'numeric 5' => [
				[1 => 1, 3 => 2, 5 => [0 => 3, 2 => 4], 6 => 5],
				[null, 1, null, 2, null, [3, null, 4], 5, null],
			],
			'associative 1' => [
				[],
				["foo" => null],
			],
			'associative 2' => [
				[],
				["foo" => null, ["bar" => null]],
			],
			'associative 3' => [
				["bar" => 1],
				["foo" => null, "bar" => 1],
			],
			'associative 4' => [
				["bar" => 1, "caw" => 2],
				["foo" => null, "bar" => 1, "baz" => null, "caw" => 2, "doo" => null],
			],
			'associative 5' => [
				["bar" => 1, "caw" => 2, "ere" => ["foo" => 3, "baz" => 4], "moo" => 5],
				["foo" => null, "bar" => 1, "baz" => null, "caw" => 2, "doo" => null, "ere" => ["foo" => 3, "bar" => null, "baz" => 4], "moo" => 5, "boo" => null],
			],
		];
	}
}