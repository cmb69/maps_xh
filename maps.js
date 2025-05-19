var conf = JSON.parse(document.querySelector("#map").dataset.mapsConf);

var map = L.map('map').setView([51.505, -0.09], 13);
if (conf.loadTiles) {
    L.tileLayer(conf.tileUrl, {
        maxZoom: 19,
        attribution: conf.tileAttribution,
    }).addTo(map);
}
