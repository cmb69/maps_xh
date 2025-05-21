<?php

namespace Maps;

use ApprovalTests\Approvals;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore2 as DocumentStore;
use Plib\FakeSystemChecker;
use Plib\SystemChecker;
use Plib\View;

class InfoCommandTest extends TestCase
{
    private DocumentStore $store;
    private SystemChecker $systemChecker;
    private View $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->store = new DocumentStore(vfsStream::url("root/"));
        $this->systemChecker = new FakeSystemChecker();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["maps"]);
    }

    private function sut(): InfoCommand
    {
        return new InfoCommand("./", $this->store, $this->systemChecker, $this->view);
    }

    public function testRendersPluginInfo(): void
    {
        $response = $this->sut()();
        $this->assertSame("Maps 1.0-dev", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
