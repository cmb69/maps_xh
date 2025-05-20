<?php

namespace Maps\Model;

use PHPUnit\Framework\TestCase;

/** @small */
class MapTest extends TestCase
{
    public function testCanRoundtrip(): void
    {
        $map = new Map("london", "Map of London", 51.505, -0.09, 13, 19, "1/1");
        $map->addMarker(51.505, -0.09, "some info", true);
        $actual = Map::fromString($map->toString(), "london.xml");
        $this->assertEquals($map, $actual);
    }
}
