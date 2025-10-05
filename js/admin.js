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

// @ts-check

/**
 * @typedef {object} Marker
 * @property {number} latitude
 * @property {number} longitude
 * @property {string} info
 * @property {boolean} show
 */

var editor = {
    /** @type {HTMLElement} */
    element: undefined,
    /** @type {HTMLTextAreaElement} */
    get textarea() {
        return this.element.querySelector("textarea[name=markers]");
    },
    /** @type {HTMLTableSectionElement} */
    get tbody() {
        return this.element.querySelector("tbody");
    },
    /** @type {HTMLTemplateElement} */
    get rowTemplate() {
        return this.element.querySelector("template.maps_row_template");
    },
    /** @type {(article: HTMLElement) => void} */
    init: function (article) {
        this.element = article;
        var textarea = this.textarea;
        textarea.closest("p").style.display = "none";
        (function () {
            var script = /** @type {HTMLScriptElement} */ (
                article.querySelector("script.maps_table_template")
            );
            article.querySelector(".maps_controls").insertAdjacentHTML("beforebegin", script.text);
        })();

        this.hydrateMarkerRows();
        /** @type {HTMLButtonElement} */ (article.querySelector("button.maps_add_row")).onclick =
            this.addMarkerRow.bind(this);
        this.tbody.onclick = this.onTBodyClick.bind(this);
        textarea.form.onsubmit = this.dehydrateMarkerRows.bind(this);
    },
    /** @type {(ev: Event) => void} */
    onTBodyClick: function (ev) {
        var button = /** @type {Element} */ (ev.target).closest(".maps_delete_row");
        if (button) {
            this.deleteMarkerRow(button.closest("tr"));
        }
    },
    /** @type {() => void} */
    hydrateMarkerRows: function () {
        var fragment = document.createDocumentFragment();
        /** @type {Marker[]} */ (JSON.parse(this.textarea.value)).forEach(
            this.hydrateMarkerRow.bind(this, fragment)
        );
        this.tbody.append(fragment);
    },
    /** @type {(fragment: DocumentFragment, marker: Marker) => void} */
    hydrateMarkerRow: function (fragment, marker) {
        fragment.append(this.rowTemplate.content.cloneNode(true));
        var row = /** @type {HTMLTableRowElement} */ (fragment.querySelector("tr:last-child"));
        Object.keys(marker).forEach(function (key) {
            var value = marker[key];
            var control = /** @type {HTMLInputElement|HTMLTextAreaElement} */ (
                row.querySelector("[name=" + key + "]")
            );
            if (control.type !== "checkbox") {
                control.value = value;
            } else {
                /** @type {HTMLInputElement} */ (control).checked = value;
            }
        });
    },
    /** @type {() => void} */
    dehydrateMarkerRows: function () {
        var markers = /** @type {Marker[]} */ ([]);
        this.tbody.querySelectorAll("tr").forEach(function (row) {
            var marker = /** @type {Marker} */ ({});
            /** @type {NodeListOf<HTMLInputElement|HTMLTextAreaElement>} */ (
                row.querySelectorAll("[name]")
            ).forEach(function (control) {
                if (control.type !== "checkbox") {
                    marker[control.name] = control.value;
                } else {
                    marker[control.name] = /** @type {HTMLInputElement} */ (control).checked;
                }
                control.name = "";
            });
            markers.push(marker);
        });
        this.textarea.value = JSON.stringify(markers);
    },
    /** @type {() => void} */
    addMarkerRow: function () {
        this.tbody.append(this.rowTemplate.content.cloneNode(true));
    },
    /** @type {(tr: HTMLTableRowElement) => void} */
    deleteMarkerRow: function (tr) {
        tr.remove();
    },
};

document.querySelectorAll("article.maps_edit").forEach(function (article) {
    Object.create(editor).init(article);
});
