<?php

namespace Maps;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;

class MainCommandTest extends TestCase
{
    private array $conf;
    private View $view;

    public function setUp(): void
    {
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["maps"];
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["maps"]);
    }

    private function sut(): MainCommand
    {
        return new MainCommand("./", $this->conf, $this->view);
    }

    public function testIncludesLeaflet(): void
    {
        $response = $this->sut()();
        Approvals::verifyHtml($response->hjs());
    }

    public function testRendersIntegrityIfConfigured(): void
    {
        $this->conf["leaflet_url"] = "https://unpkg.com/leaflet@1.9.4/dist/";
        $this->conf["leaflet_js_integrity"] = "sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=";
        $this->conf["leaflet_css_integrity"] = "sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=";
        $response = $this->sut()();
        $this->assertStringContainsString(
            "integrity=\"sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=\"",
            $response->hjs(),
        );
        $this->assertStringContainsString(
            "integrity=\"sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=\"",
            $response->hjs(),
        );
    }
}
