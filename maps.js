/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Maps_XH.
 *
 * Maps_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Maps_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Maps_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

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
