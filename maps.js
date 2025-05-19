var conf = JSON.parse(document.querySelector("#map").dataset.mapsConf);

var map = L.map('map').setView([conf.latitude, conf.longitude], conf.zoom);
if (conf.loadTiles) {
    L.tileLayer(conf.tileUrl, {
        maxZoom: conf.maxZoom,
        attribution: conf.tileAttribution,
    }).addTo(map);
}
for (var marker of conf.markers) {
    L.marker([marker[0], marker[1]]).addTo(map);
}
