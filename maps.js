function init(figure) {
    var conf = JSON.parse(figure.dataset.mapsConf);

    var map = L.map(figure.querySelector("div.maps_map")).setView([conf.latitude, conf.longitude], conf.zoom);
    if (conf.loadTiles) {
        L.tileLayer(conf.tileUrl, {
            maxZoom: conf.maxZoom,
            attribution: conf.tileAttribution,
        }).addTo(map);
    }
    for (var marker of conf.markers) {
        var m = L.marker([marker[0], marker[1]]).addTo(map);
        m.bindPopup(marker[2]);
        if (marker[3]) {
            m.openPopup();
        }
    }
}

document.querySelectorAll("figure.maps_map").forEach(init);
