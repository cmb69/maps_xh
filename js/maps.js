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

(function () {
    "use strict";

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

    var maps = /** @type {NodeListOf<HTMLElement>} */ (
        document.querySelectorAll("figure.maps_map")
    );
    maps.forEach(function (figure) {
        /** @type {() => void} */
        function resize() {
            var map = /** @type {HTMLElement} */ (figure.querySelector("div.maps_map"));
            var matches = map.dataset.aspectRatio.match(/(\d+)\/(\d+)/);
            map.style.height = (map.clientWidth / +matches[1]) * +matches[2] + "px";
        }
        var conf = /** @type {Config} */ (JSON.parse(figure.dataset.mapsConf));
        var scripts = /** @type {NodeListOf<HTMLScriptElement>} */ (
            figure.querySelectorAll("script[type='text/x-template']")
        );
        scripts.forEach(function (script) {
            script.outerHTML = script.text;
        });
        if (conf.loadTiles) {
            var image = /** @type {HTMLElement} */ (figure.querySelector("div.maps_static"));
            image.parentNode.removeChild(image);
            var map = /** @type {HTMLElement} */ (figure.querySelector("div.maps_map"));
            map.style.display = "";
            if (map.style.aspectRatio === undefined) {
                resize();
                addEventListener("resize", resize);
            }
            var leafletMap = L.map(map).setView([conf.latitude, conf.longitude], conf.zoom);
            L.tileLayer(conf.tileUrl, {
                maxZoom: conf.maxZoom,
                attribution: conf.tileAttribution,
            }).addTo(leafletMap);
            conf.markers.forEach(function (marker) {
                var m = L.marker([marker[0], marker[1]]).addTo(leafletMap);
                m.bindPopup(marker[2]);
                if (marker[3]) {
                    m.openPopup();
                }
            });
        }
    });
})();
