<?php

namespace Maps\Model;

use PHPUnit\Framework\TestCase;

class TileCalculatorTest extends TestCase
{
    private function sut(): TileCalculator
    {
        return new TileCalculator();
    }

    public function testBingen()
    {
        $this->assertEquals(
            [
                (object) ["tileX" => 4276, "tileY" => 2778, "zoom" => 13, "x" => -170, "y" => -251],
                (object) ["tileX" => 4277, "tileY" => 2778, "zoom" => 13, "x" => 86, "y" => -251],
                (object) ["tileX" => 4276, "tileY" => 2779, "zoom" => 13, "x" => -170, "y" => 5],
                (object) ["tileX" => 4277, "tileY" => 2779, "zoom" => 13, "x" => 86, "y" => 5],
            ],
            $this->sut()->calculate(49.96675, 7.96675, 13, 320, 240)
        );
    }
}
