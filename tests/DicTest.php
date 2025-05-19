<?php

namespace Maps;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_cf, $plugin_tx;
        $pth = ["folder" => ["plugins" => ""]];
        $plugin_cf = ["maps" => []];
        $plugin_tx = ["maps" => []];
    }

    public function testMakesMainCommand(): void
    {
        $this->assertInstanceOf(MainCommand::class, Dic::mainCommand());
    }

    public function testMakesMapCommand(): void
    {
        $this->assertInstanceOf(MapCommand::class, Dic::mapCommand());
    }

    public function testMakesInfoCommand(): void
    {
        $this->assertInstanceOf(InfoCommand::class, Dic::infoCommand());
    }
}
