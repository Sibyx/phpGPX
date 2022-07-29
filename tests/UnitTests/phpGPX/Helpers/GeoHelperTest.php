<?php
/**
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace UnitTests\phpGPX\Helpers;

use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\Point;
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
	public function testGetDistance()
	{
		$point1 = new Point(Point::WAYPOINT);
		$point1->latitude = 48.1573923225717;
		$point1->longitude = 17.0547121910204;

		$point2 = new Point(Point::WAYPOINT);
		$point2->latitude = 48.1644916381763;
		$point2->longitude = 17.0591753907502;

		$this->assertEquals(
			856.97,
			GeoHelper::getRawDistance($point1, $point2),
			"Invalid distance between two points!",
			1
		);
	}

	/**
	 * @link http://cosinekitty.com/compass.html
	 */
	public function testRealDistance()
	{
		$point1 = new Point(Point::WAYPOINT);
		$point1->latitude = 48.1573923225717;
		$point1->longitude = 17.0547121910204;
		$point1->elevation = 100;

		$point2 = new Point(Point::WAYPOINT);
		$point2->latitude = 48.1644916381763;
		$point2->longitude = 17.0591753907502;
		$point2->elevation = 200;

		$this->assertEquals(
			856.97,
			GeoHelper::getRawDistance($point1, $point2),
			"Invalid distance between two points!",
			1
		);

		$this->assertEquals(
			862,
			GeoHelper::getRealDistance($point1, $point2),
			"Invalid real distance between two points!",
			1
		);
	}
}
