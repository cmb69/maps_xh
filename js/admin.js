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

/** @type {(element: Element, tagName: string) => Element} */
function closest(element, tagName) {
    var parent = element;
    while (parent.tagName !== tagName.toUpperCase()) {
        parent = parent.parentElement;
    }
    return parent;
}

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
    /** @type {HTMLButtonElement} */
    get addMarkerButton() {
        return this.element.querySelector("button.maps_add_row");
    },
    /** @type {HTMLScriptElement} */
    get rowTemplate() {
        return this.element.querySelector("script.maps_row_template");
    },
    /** @type {(article: HTMLElement) => void} */
    init: function (article) {
        this.element = article;
        var textarea = this.textarea;
        /** @type {HTMLParagraphElement} */ (closest(textarea, "p")).style.display = "none";
        /** @type {HTMLScriptElement[]} */ (
            array(article.querySelectorAll("script[type='text/x-template']"))
        ).forEach(function (script) {
            script.outerHTML = script.text;
        });
        this.hydrateMarkerRows();
        this.addMarkerButton.onclick = this.addMarkerRow.bind(this);
        this.tbody.onclick = this.onTBodyClick.bind(this);
        textarea.form.onsubmit = this.dehydrateMarkerRows.bind(this);
    },
    /** @type {(ev: Event) => void} */
    onTBodyClick: function (ev) {
        var button = closest(/** @type {Element} */ (ev.target), "button");
        if (button) {
            this.deleteMarkerRow(/** @type {HTMLTableRowElement} */ (closest(button, "tr")));
        }
    },
    /** @type {() => void} */
    hydrateMarkerRows: function () {
        var tbody = this.tbody;
        /** @type {Marker[]} */ (JSON.parse(this.textarea.value)).forEach(
            this.hydrateMarkerRow.bind(this, tbody)
        );
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
                /** @type {HTMLInputElement} */ (control).checked = /** @type {boolean} */ (value);
            }
        });
    },
    /** @type {() => void} */
    dehydrateMarkerRows: function () {
        var markers = /** @type {Marker[]} */ ([]);
        array(this.tbody.querySelectorAll("tr")).forEach(function (row) {
            var marker = /** @type {Marker} */ ({});
            /** @type {(HTMLInputElement|HTMLTextAreaElement)[]} */ (
                array(row.querySelectorAll("[name]"))
            ).forEach(function (control) {
                if (control.type !== "checkbox") {
                    // @ts-ignore
                    marker[control.name] = control.value;
                } else {
                    // @ts-ignore
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
        this.tbody.insertAdjacentHTML("beforeend", this.rowTemplate.text);
    },
    /** @type {(tr: HTMLTableRowElement) => void} */
    deleteMarkerRow: function (tr) {
        tr.remove();
    },
};

array(document.querySelectorAll("article.maps_edit")).forEach(function (article) {
    Object.create(editor).init(article);
});
