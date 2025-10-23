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
    var editorProto = Object.seal({
        /** @readonly @type {HTMLElement} */
        element: undefined,

        /** @type {HTMLTextAreaElement} */
        get textarea() {
            return this.element.querySelector("textarea[name=markers]");
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
            this.hydrateMarkerRows();
            this.element.addEventListener("click", this);
            this.element.addEventListener("submit", this);
        },

        /** @type {(event: Event) => void} */
        handleEvent: function (event) {
            switch (event.type) {
                case "click":
                    return this.handleClickEvent(event);
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
        var editor = /** @type {typeof editorProto} */ (
            Object.create(editorProto, { element: { value: article } })
        );
        editor.init();
    });
})();
