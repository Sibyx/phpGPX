<?php

namespace phpGPX\Models;

use PHPUnit\Framework\TestCase;

class BoundsTest extends TestCase
{

    protected Bounds $bounds;

    protected function setUp(): void
    {
        // Example object
        $this->bounds = new Bounds(
            49.072489,
            18.814543,
            49.090543,
            18.886939
        );
    }
}