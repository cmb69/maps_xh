<?php

namespace Maps;

use ApprovalTests\Approvals;
use Maps\Model\Map;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore;
use Plib\FakeRequest;
use Plib\View;

class MapAdminCommandTest extends TestCase
{
    private DocumentStore $store;
    private View $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->store = new DocumentStore(vfsStream::url("root/"));
        $map = Map::update("london", $this->store);
        $map->addMarker(0, 0, "basic info", true);
        $this->store->commit();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["maps"]);
    }

    private function sut(): MapAdminCommand
    {
        return new MapAdminCommand($this->store, $this->view);
    }

    public function testRendersOverview(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()($request);
        $this->assertSame("Maps – Administration", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testRendersEditorForNewMap(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&maps&admin=plugin_main&action=create"]);
        $response = $this->sut()($request);
        $this->assertSame("Maps – Administration", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testCreatesNewMap(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=create",
            "post" => [
                "maps_do" => "",
                "name" => "new",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertFileExists(vfsStream::url("root/new.xml"));
        $this->assertSame("http://example.com/?&maps&admin=plugin_main", $response->location());
    }

    public function testReportsFailureToSaveNewMap(): void
    {
        vfsStream::setQuota(0);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=create",
            "post" => [
                "maps_do" => "",
                "name" => "new",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("Cannot save the map!", $response->output());
    }

    public function testRendersEditorForUpdate(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update&maps_map=london",
        ]);
        $response = $this->sut()($request);
        $this->assertSame("Maps – Administration", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testUpdatesMap(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update&maps_map=london",
            "post" => [
                "maps_do" => "",
                "markers" => "0|0|more info|",
            ],
        ]);
        $response = $this->sut()($request);
        $map = Map::retrieve("london", $this->store);
        $this->assertCount(1, $map->markers());
        $this->assertSame("http://example.com/?&maps&admin=plugin_main&maps_map=london", $response->location());
    }

    public function testReportsFailureToUpdateMap(): void
    {
        vfsStream::setQuota(0);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update&maps_map=london",
            "post" => [
                "maps_do" => "",
                "markers" => "0|0|more info|",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("Cannot save the map!", $response->output());
    }
}
