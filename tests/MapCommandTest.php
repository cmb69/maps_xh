<?php

namespace Maps;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;

class MapCommandTest extends TestCase
{
    private array $conf;
    private View $view;

    public function setUp(): void
    {
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["maps"];
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["maps"]);
    }

    private function sut(): MapCommand
    {
        return new MapCommand("../", $this->conf, $this->view);
    }

    public function testShowsMap(): void
    {
        $response = $this->sut()();
        Approvals::verifyHtml($response->output());
    }
}
