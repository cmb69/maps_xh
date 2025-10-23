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
            Object.keys(marker).forEach(function (key) {
                var value = marker[/** @type {keyof Marker} */ (key)];
                var control = /** @type {HTMLInputElement|HTMLTextAreaElement} */ (
                    row.querySelector("[name=" + key + "]")
                );
                if (control.type !== "checkbox") {
                    control.value = /** @type {string} */ (value);
                } else {
                    var input = /** @type {HTMLInputElement} */ (control);
                    input.checked = /** @type {boolean} */ (value);
                }
            });
        },

        /** @type {() => void} */
        dehydrateMarkerRows: function () {
            var markers = /** @type {Marker[]} */ ([]);
            this.tbody.querySelectorAll("tr").forEach(function (row) {
                var marker = /** @type {Marker} */ ({});
                var controls = /** @type {NodeListOf<HTMLInputElement|HTMLTextAreaElement>} */ (
                    row.querySelectorAll("[name]")
                );
                controls.forEach(function (control) {
                    if (control.type !== "checkbox") {
                        // @ts-ignore
                        marker[control.name] = control.value;
                    } else {
                        var input = /** @type {HTMLInputElement} */ (control);
                        // @ts-ignore
                        marker[control.name] = input.checked;
                    }
                    control.name = "";
                });
                markers.push(marker);
            });
            this.textarea.value = JSON.stringify(markers);
        },

        /** @type {() => void} */
        addMarkerRow: function () {
            this.tbody.insertAdjacentHTML("beforeend", this.rowTemplate.text);
        },

        /** @type {(tr: HTMLTableRowElement) => void} */
        deleteMarkerRow: function (tr) {
            tr.parentNode.removeChild(tr);
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
