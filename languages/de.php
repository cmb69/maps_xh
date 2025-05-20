<?php

$plugin_tx['maps']['tile_attribution']="&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>";

$plugin_tx['maps']['menu_main']="Verwaltung";

$plugin_tx['maps']['label_agree']="Ich stimme zu!";
$plugin_tx['maps']['label_aspect_ratio']="Seitenverhältnis";
$plugin_tx['maps']['label_edit']="Bearbeiten";
$plugin_tx['maps']['label_latitude']="Breitengrad";
$plugin_tx['maps']['label_longitude']="Längengrad";
$plugin_tx['maps']['label_markers']="Markierungen";
$plugin_tx['maps']['label_max_zoom']="Max. Zoom";
$plugin_tx['maps']['label_name']="Name";
$plugin_tx['maps']['label_new']="Neu";
$plugin_tx['maps']['label_save']="Speichern";
$plugin_tx['maps']['label_title']="Überschrift";
$plugin_tx['maps']['label_zoom']="Zoom";

$plugin_tx['maps']['help_aspect_ratio']="(Breite/Höhe)";
$plugin_tx['maps']['help_latitude']="(zwischen -90 und 90)";
$plugin_tx['maps']['help_longitude']="(zwischen -180 und 180)";
$plugin_tx['maps']['help_name']="(nur a-z, 0-9 und Bindestriche)";
$plugin_tx['maps']['help_zoom']="(zwischen 0 und 20)";

$plugin_tx['maps']['message_tile_privacy']="Um die Landkarte anzuzeigen, müssen Kachelbilder von einem Server einer Drittpartei geladen werden. Stimmen Sie dem zu?";

$plugin_tx['maps']['error_not_authorized']="Sie sind nicht befugt diese Aktion durchzuführen!";
$plugin_tx['maps']['error_no_map']="Sie haben keine Landkarte ausgewählt!";
$plugin_tx['maps']['error_load']="Die Landkarte „%s“ kann nicht geladen werden!";
$plugin_tx['maps']['error_save']="Die Landkarte kann nicht gespeichert werden!";

$plugin_tx['maps']['syscheck_title']="System-Prüfung";
$plugin_tx['maps']['syscheck_phpversion']="PHP Version ≥ %s: %s";
$plugin_tx['maps']['syscheck_plibversion']="Plib_XH Version ≥ %s: %s";
$plugin_tx['maps']['syscheck_xhversion']="CMSimple_XH Version ≥ %s: %s";
$plugin_tx['maps']['syscheck_writable']="%s ist schreibbar: %s";
$plugin_tx['maps']['syscheck_good']="gut";
$plugin_tx['maps']['syscheck_bad']="schlecht";

$plugin_tx['maps']['cf_leaflet_url']="Die Basis-URL der Leaflet-Assets. Leer lassen, um die gebündelte Verions zu verwenden (empfohlen aus Datenschutzgründen). Soll ein öffentliches CDN verwendet werden, ist die URL hier einzugeben, z.B. https://unpkg.com/leaflet@1.9.4/dist/.";
$plugin_tx['maps']['cf_leaflet_js_integrity']="Wird Leaflet per CDN geladen, kann hier ein Hashwert eingetragen werden, um die Integrität des JS zu prüfen. Siehe https://developer.mozilla.org/de/docs/Web/Security/Subresource_Integrity zu Details.";
$plugin_tx['maps']['cf_leaflet_css_integrity']="Wird Leaflet per CDN geladen, kann hier ein Hashwert eingetragen werden, um die Integrität des CSS zu prüfen. Siehe https://developer.mozilla.org/de/docs/Web/Security/Subresource_Integrity zu Details.";
$plugin_tx['maps']['cf_tile_privacy']="Die eigentlichen Inhalte der Landkarten werden aus Kachelbildern erstellt, die von einem Server einer Drittpartei (siehe Tile → Url) geladen werden. Dies kann Datenschutzrechte Ihrer Besucher verletzen. Daher wird empfohlen diese Einstellung zu aktivieren, in welchem Fall die Kachelbilder erst geladen werden, nachdem Besucher ausdrücklich zugestimmt haben.";
$plugin_tx['maps']['cf_tile_url']="Die URL-Vorlage für die Kachelbilder. Wird diese geändert, muss möglicherweise auch die Spracheinstellung Tile → Attribution angepasst werden.";

?>
