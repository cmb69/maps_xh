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

(function () {
    "use strict";

    /**
     * @typedef {object} Marker
     * @property {number} latitude
     * @property {number} longitude
     * @property {string} info
     * @property {boolean} show
     */

    /** @type {<T>(arrayLike: ArrayLike<T>) => T[]} */
    function array(arrayLike) {
        return Array.prototype.slice.call(arrayLike);
    }

    /** @readonly */
    var Editor = Object.seal({
        /** @readonly @type {HTMLElement} */
        element: undefined,

        /** @type {HTMLTextAreaElement} */
        get textarea() {
            return this.element.querySelector("textarea[name=markers]");
        },

        /** @type {HTMLInputElement} */
        get latitude() {
            return this.element.querySelector("input[name=latitude");
        },

        /** @type {HTMLInputElement} */
        get longitude() {
            return this.element.querySelector("input[name=longitude");
        },

        /** @type {HTMLInputElement} */
        get zoom() {
            return this.element.querySelector("input[name=zoom");
        },

        /** @type {HTMLInputElement} */
        get geoUri() {
            return this.element.querySelector("input[name=geo_uri");
        },

        /** @type {HTMLTableSectionElement} */
        get tbody() {
            return this.element.querySelector("tbody");
        },

        /** @type {HTMLScriptElement} */
        get rowTemplate() {
            return this.element.querySelector("script.maps_row_template");
        },

        /** @type {() => void} */
        init: function () {
            var textarea = this.textarea;
            textarea.closest("p").style.display = "none";
            var scripts = /** @type {NodeListOf<HTMLScriptElement>} */ (
                this.element.querySelectorAll("script[type='text/x-template']")
            );
            scripts.forEach(function (script) {
                script.outerHTML = script.text;
            });
            this.buildGeoUri();
            this.hydrateMarkerRows();
            this.element.addEventListener("click", this);
            this.element.addEventListener("change", this);
            this.element.addEventListener("submit", this);
        },

        /** @type {(event: Event) => void} */
        handleEvent: function (event) {
            switch (event.type) {
                case "click":
                    return this.handleClickEvent(event);
                case "change":
                    return this.handleChangeEvent(event);
                case "submit":
                    return this.dehydrateMarkerRows();
            }
        },

        /** @type {(event: Event) => void} */
        handleClickEvent: function (event) {
            var target = /** @type {Element} */ (event.target);
            var button = target.closest("button");
            if (!button) return;
            switch (button.classList[0]) {
                case "maps_add_row":
                    return this.addMarkerRow();
                case "maps_delete_row":
                    return this.deleteMarkerRow(button.closest("tr"));
            }
        },

        /** @type {(event: Event) => void} */
        handleChangeEvent: function (event) {
            var target = /** @type {Element} */ (event.target);
            var input = target.closest("input");
            if (!input) return;
            switch (input.name) {
                case "latitude":
                case "longitude":
                case "zoom":
                    return this.buildGeoUri();
                case "geo_uri":
                    return this.parseGeoUri();
            }
        },

        buildGeoUri: function () {
            var latitude = this.latitude.value;
            var longitude = this.longitude.value;
            var zoom = this.zoom.value;
            this.geoUri.value = "geo:" + latitude + "," + longitude + "?z=" + zoom;
        },

        parseGeoUri: function () {
            var geoUri = this.geoUri.value;
            var matches = geoUri.match(/^geo:([\d.]+),([\d.]+)\?z=(\d+)$/);
            if (!matches) return;
            var latitude = matches[1];
            var longitude = matches[2];
            var zoom = matches[3];
            this.latitude.value = latitude;
            this.longitude.value = longitude;
            this.zoom.value = zoom;
        },

        /** @type {() => void} */
        hydrateMarkerRows: function () {
            var tbody = this.tbody;
            var markers = /** @type {Marker[]} */ (JSON.parse(this.textarea.value));
            markers.forEach(this.hydrateMarkerRow.bind(this, tbody));
        },

        /** @type {(tbody: HTMLTableSectionElement, marker: Marker) => void} */
        hydrateMarkerRow: function (tbody, marker) {
            tbody.insertAdjacentHTML("beforeend", this.rowTemplate.text);
            var row = /** @type {HTMLTableRowElement} */ (tbody.querySelector("tr:last-child"));
            this.markerLatitude(row).value = marker.latitude.toString();
            this.markerLongitude(row).value = marker.longitude.toString();
            this.markerInfo(row).value = marker.info;
            this.markerShow(row).checked = marker.show;
        },

        /** @type {() => void} */
        dehydrateMarkerRows: function () {
            var rows = array(this.tbody.querySelectorAll("tr"));
            var markers = rows.map(this.dehydrateMarkerRow.bind(this));
            this.textarea.value = JSON.stringify(markers);
            this.geoUri.name = "";
            var controls = /** @type {NodeListOf<HTMLInputElement|HTMLTextAreaElement>} */ (
                this.tbody.querySelectorAll("[name]")
            );
            controls.forEach(function (el) {
                el.name = "";
            });
        },

        /** @type {(row: HTMLTableRowElement) => Marker} */
        dehydrateMarkerRow: function (row) {
            return {
                latitude: +this.markerLatitude(row).value,
                longitude: +this.markerLongitude(row).value,
                info: this.markerInfo(row).value,
                show: this.markerShow(row).checked,
            };
        },

        /** @type {() => void} */
        addMarkerRow: function () {
            this.tbody.insertAdjacentHTML("beforeend", this.rowTemplate.text);
        },

        /** @type {(tr: HTMLTableRowElement) => void} */
        deleteMarkerRow: function (tr) {
            tr.parentNode.removeChild(tr);
        },

        /** @type {(row: HTMLTableRowElement) => HTMLInputElement} */
        markerLatitude: function (row) {
            return row.querySelector("input[name=latitude]");
        },

        /** @type {(row: HTMLTableRowElement) => HTMLInputElement} */
        markerLongitude: function (row) {
            return row.querySelector("input[name=longitude]");
        },

        /** @type {(row: HTMLTableRowElement) => HTMLTextAreaElement} */
        markerInfo: function (row) {
            return row.querySelector("textarea[name=info]");
        },

        /** @type {(row: HTMLTableRowElement) => HTMLInputElement} */
        markerShow: function (row) {
            return row.querySelector("input[name=show]");
        },
    });

    var editors = /** @type {NodeListOf<HTMLElement>} */ (
        document.querySelectorAll("article.maps_edit")
    );
    editors.forEach(function (article) {
        var editor = /** @type {typeof Editor} */ (
            Object.create(Editor, { element: { value: article } })
        );
        editor.init();
    });
})();
