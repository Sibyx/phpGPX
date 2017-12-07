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
	}

	public function testFloatOrNull()
	{
		$this->assertNull(SerializationHelper::floatOrNull(""));
		$this->assertNull(SerializationHelper::floatOrNull(null));
		$this->assertNull(SerializationHelper::floatOrNull("BLA"));
		$this->assertInternalType("float", SerializationHelper::floatOrNull(5.6));
		$this->assertInternalType("float", SerializationHelper::floatOrNull(5));
	}

	public function testStringOrNull()
	{
		$this->assertNull(SerializationHelper::stringOrNull(null));
		$this->assertInternalType("string", "");
		$this->assertInternalType("string", "Bla bla");
	}
}
