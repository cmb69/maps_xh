var conf = JSON.parse(document.querySelector("#map").dataset.mapsConf);

var map = L.map('map').setView([conf.latitude, conf.longitude], conf.zoom);
if (conf.loadTiles) {
    L.tileLayer(conf.tileUrl, {
        maxZoom: conf.maxZoom,
        attribution: conf.tileAttribution,
    }).addTo(map);
}
