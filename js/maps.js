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

/* globals L */
// @ts-check

/**
 * @typedef {Object} Config
 * @prop {string} tileUrl
 * @prop {string} tileAttribution
 * @prop {boolean} loadTiles
 * @prop {number} latitude
 * @prop {number} longitude
 * @prop {number} zoom
 * @prop {number} maxZoom
 * @prop {[number,number,string,boolean][]} markers
 */

/** @type {NodeListOf<HTMLElement>} */ (document.querySelectorAll("figure.maps_map")).forEach(
    function (figure) {
        let conf = /** @type {Config} */ (JSON.parse(figure.dataset.mapsConf));

        let map = L.map(figure.querySelector("div.maps_map")).setView(
            [conf.latitude, conf.longitude],
            conf.zoom
        );
        if (conf.loadTiles) {
            L.tileLayer(conf.tileUrl, {
                maxZoom: conf.maxZoom,
                attribution: conf.tileAttribution,
            }).addTo(map);
        }
        conf.markers.forEach(function (marker) {
            let [latitude, longitude, info, show] = marker;
            let m = L.marker([latitude, longitude]).addTo(map);
            m.bindPopup(info);
            if (show) {
                m.openPopup();
            }
        });
    }
);
