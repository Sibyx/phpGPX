<?php

namespace phpGPX\Tests\Unit\Parsers;

use phpGPX\Parsers\ExtensionRegistry;
use phpGPX\Parsers\Extensions\TrackPointExtensionParser;
use PHPUnit\Framework\TestCase;

class ExtensionRegistryTest extends TestCase
{
	private const GARMIN_TPE_V2 = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v2';
	private const GARMIN_TPE_V1 = 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1';

	public function testDefaultRegistersTrackPointExtension(): void
	{
		$registry = ExtensionRegistry::default();

		$this->assertTrue($registry->has(self::GARMIN_TPE_V2));
		$this->assertTrue($registry->has(self::GARMIN_TPE_V1));
		$this->assertSame(
			TrackPointExtensionParser::class,
			$registry->getParserClass(self::GARMIN_TPE_V2)
		);
	}

	public function testRegisterReturnsFluentInterface(): void
	{
		$registry = new ExtensionRegistry();
		$result = $registry->register('http://example.com/ext', TrackPointExtensionParser::class);
		$this->assertSame($registry, $result);
	}

	public function testGetParserClassReturnsNullForUnknown(): void
	{
		$registry = new ExtensionRegistry();
		$this->assertNull($registry->getParserClass('http://unknown.com/ext'));
	}

	public function testHasReturnsFalseForUnknown(): void
	{
		$registry = new ExtensionRegistry();
		$this->assertFalse($registry->has('http://unknown.com/ext'));
	}

	public function testCustomRegistration(): void
	{
		$registry = ExtensionRegistry::default()
			->register('http://example.com/custom/v1', TrackPointExtensionParser::class);

		$this->assertTrue($registry->has('http://example.com/custom/v1'));
		$this->assertTrue($registry->has(self::GARMIN_TPE_V2));
	}

	public function testAllReturnsRegisteredMappings(): void
	{
		$registry = ExtensionRegistry::default();
		$all = $registry->all();

		$this->assertArrayHasKey(self::GARMIN_TPE_V2, $all);
		$this->assertArrayHasKey(self::GARMIN_TPE_V1, $all);
		$this->assertCount(2, $all);
	}

	public function testEmptyRegistryHasNoMappings(): void
	{
		$registry = new ExtensionRegistry();
		$this->assertEmpty($registry->all());
	}

	public function testDefaultPrefixIsGpxtpx(): void
	{
		$registry = ExtensionRegistry::default();

		$this->assertSame('gpxtpx', $registry->getPrefix(self::GARMIN_TPE_V2));
		$this->assertSame('gpxtpx', $registry->getPrefix(self::GARMIN_TPE_V1));
	}

	public function testCustomPrefix(): void
	{
		$registry = (new ExtensionRegistry())
			->register('http://example.com/ext', TrackPointExtensionParser::class, 'myprefix');

		$this->assertSame('myprefix', $registry->getPrefix('http://example.com/ext'));
	}

	public function testGetPrefixReturnsNullForUnknown(): void
	{
		$registry = new ExtensionRegistry();
		$this->assertNull($registry->getPrefix('http://unknown.com/ext'));
	}

	public function testDefaultPrefixIsExt(): void
	{
		$registry = (new ExtensionRegistry())
			->register('http://example.com/ext', TrackPointExtensionParser::class);

		$this->assertSame('ext', $registry->getPrefix('http://example.com/ext'));
	}
}