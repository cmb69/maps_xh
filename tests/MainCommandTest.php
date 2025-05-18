<?php

namespace Maps;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;

class MainCommandTest extends TestCase
{
    private View $view;

    public function setUp(): void
    {
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["maps"]);
    }

    private function sut(): MainCommand
    {
        return new MainCommand($this->view);
    }

    public function testIncludesLeaflet(): void
    {
        $response = $this->sut()();
        Approvals::verifyHtml($response->hjs());
    }
}
