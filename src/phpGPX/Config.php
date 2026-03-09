<?php

namespace phpGPX;

/**
 * Class Config
 * Configuration value object for a phpGPX instance.
 * @package phpGPX
 */
class Config
{
	public function __construct(
		/** Pretty print XML and JSON output */
		public bool $prettyPrint = true,
	) {
	}
}
