<?php

$plugin_tx['maps']['tile_attribution']="&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>";

$plugin_tx['maps']['label_agree']="I agree!";

$plugin_tx['maps']['message_tile_privacy']="To display the map, tile images need to be fetched from a third-party server. Do you agree?";

$plugin_tx['maps']['syscheck_title']="System Check";
$plugin_tx['maps']['syscheck_phpversion']="PHP version ≥ %s: %s";
$plugin_tx['maps']['syscheck_plibversion']="Plib_XH version ≥ %s: %s";
$plugin_tx['maps']['syscheck_xhversion']="CMSimple_XH version ≥ %s: %s";
$plugin_tx['maps']['syscheck_writable']="%s is writable: %s";
$plugin_tx['maps']['syscheck_good']="good";
$plugin_tx['maps']['syscheck_bad']="bad";

$plugin_tx['maps']['cf_leaflet_url']="The base URL of the Leaflet assets. Leave blank to use the bundled version (recommended for data privacy reasons). If you want to use a public CDN, enter the URL, e.g. https://unpkg.com/leaflet@1.9.4/dist/.";
$plugin_tx['maps']['cf_leaflet_js_integrity']="If Leaflet is fetched from CDN, you can enter a hash value to check the integrity of the JS. See https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity for details.";
$plugin_tx['maps']['cf_leaflet_css_integrity']="If Leaflet is fetched from CDN, you can enter a hash value to check the integrity of the CSS. See https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity for details.";
$plugin_tx['maps']['cf_tile_privacy']="The actual map contents are built from tile images which are loaded from a third-party server (see Tile → Url). This may violate the data privacy of your visitors. Thus it is recommended to enable this setting, in which case tile images will only be loaded after visitors have explicitly agreed.";
$plugin_tx['maps']['cf_tile_url']="The URL template for the tile images. If you change this, you also may need to change the language setting Tile → Attribution.";

?>
