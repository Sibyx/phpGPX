<?php

namespace phpGPX\Tests\Unit\Helpers;

use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\Point;
use phpGPX\Models\PointType;
use PHPUnit\Framework\TestCase;

class GeoHelperTest extends TestCase
{
	/**
	 * Tested with https://www.freemaptools.com/measure-distance.htm
	 *
	 * Input points:
	 *  - 48.1573923225717 17.0547121910204
	 *  - 48.1644916381763 17.0591753907502
	 */
	public function testGetDistance(): void
	{
		$point1 = new Point(PointType::Waypoint);
		$point1->latitude = 48.1573923225717;
		$point1->longitude = 17.0547121910204;

		$point2 = new Point(PointType::Waypoint);
		$point2->latitude = 48.1644916381763;
		$point2->longitude = 17.0591753907502;

		$this->assertEqualsWithDelta(
			856.97,
			GeoHelper::getRawDistance($point1, $point2),
			1,
			"Invalid distance between two points!"
		);
	}

	/**
	 * @link http://cosinekitty.com/compass.html
	 */
	public function testRealDistance(): void
	{
		$point1 = new Point(PointType::Waypoint);
		$point1->latitude = 48.1573923225717;
		$point1->longitude = 17.0547121910204;
		$point1->elevation = 100;

		$point2 = new Point(PointType::Waypoint);
		$point2->latitude = 48.1644916381763;
		$point2->longitude = 17.0591753907502;
		$point2->elevation = 200;

		$this->assertEqualsWithDelta(
			856.97,
			GeoHelper::getRawDistance($point1, $point2),
			1,
			"Invalid distance between two points!"
		);

		$this->assertEqualsWithDelta(
			862,
			GeoHelper::getRealDistance($point1, $point2),
			1,
			"Invalid real distance between two points!"
		);
	}

	public function testSamePointZeroDistance(): void
	{
		$point1 = new Point(PointType::Waypoint);
		$point1->latitude = 48.1573923225717;
		$point1->longitude = 17.0547121910204;

		$point2 = new Point(PointType::Waypoint);
		$point2->latitude = 48.1573923225717;
		$point2->longitude = 17.0547121910204;

		$this->assertEqualsWithDelta(0.0, GeoHelper::getRawDistance($point1, $point2), 0.001);
	}

	public function testRealDistanceWithNullElevation(): void
	{
		$point1 = new Point(PointType::Waypoint);
		$point1->latitude = 48.1573923225717;
		$point1->longitude = 17.0547121910204;

		$point2 = new Point(PointType::Waypoint);
		$point2->latitude = 48.1644916381763;
		$point2->longitude = 17.0591753907502;

		// With null elevation, real distance should equal raw distance
		$rawDist = GeoHelper::getRawDistance($point1, $point2);
		$realDist = GeoHelper::getRealDistance($point1, $point2);
		$this->assertEqualsWithDelta($rawDist, $realDist, 0.001);
	}
}