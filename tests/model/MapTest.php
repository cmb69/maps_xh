<?php

namespace Maps\Model;

use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testCanRoundtrip(): void
    {
        $map = new Map("london", 51.505, -0.09, 13, 19);
        $map->addMarker(51.505, -0.09, "some info", true);
        $actual = Map::fromString($map->toString(), "london.xml");
        $this->assertEquals($map, $actual);
    }
}
