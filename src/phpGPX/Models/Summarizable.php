<?php
/**
 * Created            12/09/16 11:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Models;


interface Summarizable
{

	/**
	 * Serialize object to array
	 * @return array
	 */
	function summary();

}