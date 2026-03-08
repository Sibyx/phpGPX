<?php
/**
 * Created            17/02/2017 18:36
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

use phpGPX\Config;

interface StatsCalculator
{

	/**
	 * Recalculate stats objects.
	 * @param Config $config
	 * @return void
	 */
	public function recalculateStats(Config $config): void;

	/**
	 * Return all points in collection.
	 * @return Point[]
	 */
	public function getPoints(): array;
}