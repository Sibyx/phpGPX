<?php
/**
 * Created            12/09/16 11:14
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


interface Summarizable
{

	/**
	 * Return summary of object as array
	 * @return array
	 */
	function summary();

	/**
	 * Return valid XML node based on GPX standard and Garmin Extensions
	 * @return mixed
	 */
	function toNode();

}