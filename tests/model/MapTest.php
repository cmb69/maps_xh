<?php

namespace Maps\Model;

use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testCanRoundtrip(): void
    {
        $map = new Map(51.505, -0.09, 13, 19);
        $actual = Map::fromString($map->toString(), "");
        $this->assertEquals($map, $actual);
    }
}
