<?php

namespace phpGPX\Tests;

use phpGPX\phpGPX;
use PHPUnit\Framework\TestCase;

class LoadFileTest extends TestCase
{
	public function testLoadXmlFileGeneratedByTimezero()
	{
		$file = __DIR__ . '/fixtures/timezero.gpx';

		$gpx = new phpGpx();
		$gpxFile = $gpx->load($file);

		$this->assertEquals($this->createExpectedArray(), $gpxFile->toArray(), "", 0.0001);

		// Check XML generation
		$gpxFile->toXML()->saveXML();
	}

	private function createExpectedArray()
	{
		return [
			'waypoints' => [
				[
					'lat' => 49.3636333333086,
					'lon' => 0.0800866666666667,
					'time' => '2014-12-13T16:32:51+00:00',
					'name' => 'Event 0000',
					'cmt' => '',
					'extensions' => [
						'unsupported' => [
							'MxTimeZeroSymbol' => 10,
							'color' => -16744448,
						],
					],
				],
				[
					'lat' => 49.3636333333086,
					'lon' => 0.0800866666666667,
					'time' => '2014-12-13T16:32:52+00:00',
					'name' => 'Event 0001',
					'cmt' => '',
					'extensions' => [
						'unsupported' => [
							'MxTimeZeroSymbol' => 10,
							'color' => -16744448,
						],
					],
				],
			],
			'tracks' => [
				[
					'name' => 'Ownship',
					'extensions' => [
						'unsupported' => [
							'guid' => 201,
						],
					],
					'trkseg' => [
						[
							'points' => [
								[
									'lat' => 49.3635449998312,
									'lon' => 0.0801483333364938,
									'time' => '2010-01-01T14:48:37+00:00',
								],
								[
									'lat' => 49.3635350651798,
									'lon' => 0.0801416666698513,
									'time' => '2010-01-01T14:48:40+00:00',
									'difference' => 1.2055693602077022,
									'distance' => 1.2055693602077022,
								],
								[
									'lat' => 49.3635266991555,
									'lon' => 0.0801333333365323,
									'time' => '2010-01-01T14:48:46+00:00',
									'difference' => 1.1088552014759407,
									'distance' => 2.314424561683643,
								],
							],
							'stats' => [
								'distance' => 2.314424561683643,
								'realDistance' => 2.314424561683643,
								'avgSpeed' => 0.2571582846315159,
								'avgPace' => 3888.6555859279733,
								'minAltitude' => 0.0,
								'maxAltitude' => 0.0,
								'cumulativeElevationGain' => 0.0,
								'cumulativeElevationLoss' => 0.0,
								'startedAt' => '2010-01-01T14:48:37+00:00',
								'finishedAt' => '2010-01-01T14:48:46+00:00',
								'duration' => 9.0,
							],
						],
					],
					'stats' => [
						'distance' => 2.314424561683643,
						'realDistance' => 2.314424561683643,
						'avgSpeed' => 0.2571582846315159,
						'avgPace' => 3888.6555859279733,
						'minAltitude' => 0.0,
						'maxAltitude' => 0.0,
						'cumulativeElevationGain' => 0.0,
						'cumulativeElevationLoss' => 0.0,
						'startedAt' => '2010-01-01T14:48:37+00:00',
						'finishedAt' => '2010-01-01T14:48:46+00:00',
						'duration' => 9.0,
					],
				],
				[
					'name' => 'Ownship',
					'extensions' => [
						'unsupported' => [
							'guid' => 102,
						],
					],
					'trkseg' => [
						[
							'points' => [
								[
									'lat' => 49.4574117319429,
									'lon' => 0.0343682156842231,
									'time' => '2016-04-03T14:13:09+00:00',
								],
								[
									'lat' => 49.4573966992346,
									'lon' => 0.0343466078409025,
									'time' => '2016-04-03T14:13:10+00:00',
									'difference' => 2.2876315307770505,
									'distance' => 2.2876315307770505,
								],
								[
									'lat' => 49.4573700325059,
									'lon' => 0.0342948235267376,
									'time' => '2016-04-03T14:13:12+00:00',
									'difference' => 4.775098771720203,
									'distance' => 7.062730302497254,
								],
							],
							'stats' => [
								'distance' => 7.062730302497254,
								'realDistance' => 7.062730302497254,
								'avgSpeed' => 2.354243434165751,
								'avgPace' => 424.7649098167112,
								'minAltitude' => 0.0,
								'maxAltitude' => 0.0,
								'cumulativeElevationGain' => 0.0,
								'cumulativeElevationLoss' => 0.0,
								'startedAt' => '2016-04-03T14:13:09+00:00',
								'finishedAt' => '2016-04-03T14:13:12+00:00',
								'duration' => 3.0,
							],
						],
					],
					'stats' => [
						'distance' => 7.062730302497254,
						'realDistance' => 7.062730302497254,
						'avgSpeed' => 2.354243434165751,
						'avgPace' => 424.7649098167112,
						'minAltitude' => 0.0,
						'maxAltitude' => 0.0,
						'cumulativeElevationGain' => 0.0,
						'cumulativeElevationLoss' => 0.0,
						'startedAt' => '2016-04-03T14:13:09+00:00',
						'finishedAt' => '2016-04-03T14:13:12+00:00',
						'duration' => 3.0,
					],
				],
			],
		];
	}
}
