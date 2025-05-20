<?php

namespace Maps;

use ApprovalTests\Approvals;
use Maps\Model\Map;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\DocumentStore2 as DocumentStore;
use Plib\FakeRequest;
use Plib\View;

class MapAdminCommandTest extends TestCase
{
    private DocumentStore $store;
    /** @var CsrfProtector&Stub */
    private $csrfProtector;
    private View $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->store = new DocumentStore(vfsStream::url("root/"));
        $map = Map::create("london", $this->store);
        $map->addMarker(0, 0, "basic info", true);
        $this->store->commit();
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->csrfProtector->method("token")->willReturn("0123456789ABCDEF");
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["maps"]);
    }

    private function sut(): MapAdminCommand
    {
        return new MapAdminCommand("./", $this->store, $this->csrfProtector, $this->view);
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
        $this->assertSame("Maps – Edit", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testCreatesNewMap(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
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

    public function testCreatingIsCsrfProtected(): void
    {
        $this->csrfProtector->method("check")->willReturn(false);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=create",
            "post" => [
                "maps_do" => "",
                "name" => "new",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("You are not authorized to conduct this action!", $response->output());
    }

    public function testReportsFailureToSaveNewMap(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
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
        $this->assertSame("Maps – Edit", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testsReportsThatNoMapIsSelected(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update",
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("You have not selected a map!", $response->output());
    }

    public function testReportsMissingMap(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update&maps_map=does-not-exist",
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("Cannot load the map “does-not-exist”!", $response->output());
    }

    public function testUpdatesMap(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update&maps_map=london",
            "post" => [
                "maps_do" => "",
                "markers" => '[{"latitude":0,"longitude":0,"info":"more info","show":false}]',
            ],
        ]);
        $response = $this->sut()($request);
        $map = Map::read("london", $this->store);
        $this->assertCount(1, $map->markers());
        $this->assertSame("http://example.com/?&maps&admin=plugin_main&maps_map=london", $response->location());
    }

    public function testsReportsMissingMapWhenUpdating(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update&maps_map=does-not-exist",
            "post" => [
                "maps_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("Cannot load the map “does-not-exist”!", $response->output());
    }

    public function testsReportsThatNoMapIsSelectedWhenUpdating(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update",
            "post" => [
                "maps_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("You have not selected a map!", $response->output());
    }

    public function testUpdateIsCsrfProtected(): void
    {
        $this->csrfProtector->method("check")->willReturn(false);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=update&maps_map=london",
            "post" => [
                "maps_do" => "",
                "markers" => "0|0|more info|",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("You are not authorized to conduct this action!", $response->output());
    }

    public function testReportsFailureToUpdateMap(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
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

    public function testRendersImportForm(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import&maps_map=london",
        ]);
        $response = $this->sut()($request);
        $this->assertSame("Maps – Import", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testsReportsThatNoMapIsSelectedForImport(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import",
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("You have not selected a map!", $response->output());
    }

    public function testReportsMissingMapForImport(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import&maps_map=does-not-exist",
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("Cannot load the map “does-not-exist”!", $response->output());
    }

    public function testImportsGeoJson(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import&maps_map=london",
            "post" => [
                "maps_do" => "",
                "geojson" => '{"features":[{"properties":{"foo":"bar"},'
                    . '"geometry":{"type":"Point","coordinates":[0,0]}}]}',
                "template" => "{foo}",
            ],
        ]);
        $response = $this->sut()($request);
        $map = Map::read("london", $this->store);
        $this->assertCount(2, $map->markers());
        $this->assertSame("http://example.com/?&maps&admin=plugin_main&maps_map=london", $response->location());
    }

    public function testsReportsThatNoMapIsSelectedWhenImporting(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import",
            "post" => [
                "maps_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("You have not selected a map!", $response->output());
    }

    public function testsReportsMissingMapWhenImporting(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import&maps_map=does-not-exist",
            "post" => [
                "maps_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("Cannot load the map “does-not-exist”!", $response->output());
    }

    public function testImportIsCsrfProtected(): void
    {
        $this->csrfProtector->method("check")->willReturn(false);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import&maps_map=london",
            "post" => [
                "maps_do" => "",
                "geojson" => '{}',
                "template" => "{foo}",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("You are not authorized to conduct this action!", $response->output());
    }

    public function testsReportsUnsupportedGeoJson(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import&maps_map=london",
            "post" => [
                "maps_do" => "",
                "geojson" => '{}',
                "template" => "{foo}",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("This GeoJSON is not supported!", $response->output());
    }

    public function testReportsFailureToImport(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        vfsStream::setQuota(0);
        $request = new FakeRequest([
            "url" => "http://example.com/?&maps&admin=plugin_main&action=import&maps_map=london",
            "post" => [
                "maps_do" => "",
                "geojson" => '{"features":[{"properties":{"foo":"bar"},'
                    . '"geometry":{"type":"Point","coordinates":[0,0]}}]}',
                "template" => "{foo}",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("Cannot save the map!", $response->output());
    }
}
