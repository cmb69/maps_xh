<?php

namespace Maps;

use ApprovalTests\Approvals;
use Maps\Model\Map;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore2 as DocumentStore;
use Plib\FakeRequest;
use Plib\View;

class MapCommandTest extends TestCase
{
    private array $conf;
    private DocumentStore $store;
    private View $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["maps"];
        $this->store = new DocumentStore(vfsStream::url("root/"));
        $map = Map::create("london", $this->store);
        $map->setCoordinates(-0.09, 51.505);
        $map->setZoom(13, 19);
        $map->addMarker(51.505, -0.09, "some info", true);
        $this->store->commit();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["maps"]);
    }

    private function sut(): MapCommand
    {
        return new MapCommand("../", $this->conf, $this->store, $this->view);
    }

    public function testShowsMap(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()("london", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testReportsNonExistingMap(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()("does-not-exist", $request);
        $this->assertStringContainsString("There is no “does-not-exist” map!", $response->output());
    }

    public function testDoesNotShowPrivacyFormIfConfigured(): void
    {
        $this->conf["tile_privacy"] = "";
        $request = new FakeRequest();
        $response = $this->sut()("london", $request);
        $this->assertStringNotContainsString("<form", $response->output());
    }

    public function testAgreementSetsCookieAndRedirects(): void
    {
        $request = new FakeRequest(["post" => ["maps_agree" => "1"]]);
        $response = $this->sut()("london", $request);
        $this->assertEquals(["maps_agreed", "1", 0], $response->cookie());
        $this->assertSame("http://example.com/", $response->location());
    }
}
