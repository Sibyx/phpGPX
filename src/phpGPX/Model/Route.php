<?php
/**
 * Created            26/08/16 14:21
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Model;


class Route
{

	/** @var  string */
	private $name;

	/** @var  Segment[] */
	private $segments;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	public function getAllPoints()
	{
		$points = [];

		foreach ($this->segments as $segment)
		{
			foreach ($segment->points as $point)
			{

			}
		}

	}



}