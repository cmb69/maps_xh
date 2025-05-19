<?php

namespace Maps;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
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
        $request = new FakeRequest();
        $response = $this->sut()($request);
        Approvals::verifyHtml($response->output());
    }

    public function testDoesNotShowPrivacyFormIfConfigured(): void
    {
        $this->conf["tile_privacy"] = "";
        $request = new FakeRequest();
        $response = $this->sut()($request);
        $this->assertStringNotContainsString("<form", $response->output());
    }

    public function testAgreementSetsCookieAndRedirects(): void
    {
        $request = new FakeRequest(["post" => ["maps_agree" => "1"]]);
        $response = $this->sut()($request);
        $this->assertEquals(["maps_agreed", "1", 0], $response->cookie());
        $this->assertSame("http://example.com/", $response->location());
    }
}
