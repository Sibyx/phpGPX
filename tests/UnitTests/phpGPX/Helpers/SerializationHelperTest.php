<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace UnitTests\phpGPX\Helpers;

use phpGPX\Helpers\SerializationHelper;
use PHPUnit\Framework\TestCase;

class SerializationHelperTest extends TestCase
{
	public function testIntegerOrNull()
	{
		$this->assertNull(SerializationHelper::integerOrNull(""));
		$this->assertNull(SerializationHelper::integerOrNull(null));
		$this->assertNull(SerializationHelper::integerOrNull("BLA"));
		$this->assertInternalType("int", SerializationHelper::integerOrNull(5));
		$this->assertInternalType("int", SerializationHelper::integerOrNull("5"));
	}

	public function testFloatOrNull()
	{
		$this->assertNull(SerializationHelper::floatOrNull(""));
		$this->assertNull(SerializationHelper::floatOrNull(null));
		$this->assertNull(SerializationHelper::floatOrNull("BLA"));
		$this->assertInternalType("float", SerializationHelper::floatOrNull(5.6));
		$this->assertInternalType("float", SerializationHelper::floatOrNull(5));
		$this->assertInternalType("float", SerializationHelper::floatOrNull("5.6"));
		$this->assertInternalType("float", SerializationHelper::floatOrNull("5"));
	}

	public function testStringOrNull()
	{
		$this->assertNull(SerializationHelper::stringOrNull(null));
		$this->assertInternalType("string", SerializationHelper::stringOrNull(""));
		$this->assertInternalType("string", SerializationHelper::stringOrNull("Bla bla"));
	}

	/**
	 * @dataProvider dataProviderFilterNotNull
	 */
	public function testFilterNotNull($expected, $actual)
	{
		$this->assertEquals($expected, SerializationHelper::filterNotNull($actual));
	}

	public function dataProviderFilterNotNull()
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
