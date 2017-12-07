<?php
/**
 * Created            12/09/16 11:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;

/**
 * Interface Summarizable
 * @package phpGPX\Models
 */
interface Summarizable
{

	/**
	 * Serialize object to array
	 * @return array
	 */
	public function toArray();
}
