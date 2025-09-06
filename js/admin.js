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

document.querySelectorAll("article.maps_edit").forEach(function (article) {
    var textarea = /** @type {HTMLTextAreaElement} */ (
        article.querySelector("textarea[name=markers]")
    );
    textarea.closest("p").style.display = "none";
    article
        .querySelector(".maps_controls")
        .insertAdjacentHTML(
            "beforebegin",
            /** @type {HTMLScriptElement} */ (article.querySelector("script.maps_table_template"))
                .text
        );
    var tbody = article.querySelector("tbody");
    var rowTemplate = /** @type {HTMLTemplateElement} */ (
        article.querySelector("template.maps_row_template")
    );
    /** @type {Marker[]} */ (JSON.parse(textarea.value)).forEach(function (marker) {
        tbody.append(rowTemplate.content.cloneNode(true));
        var row = /** @type {HTMLTableRowElement} */ (tbody.querySelector("tr:last-child"));
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
    });
    /** @type {HTMLButtonElement} */ (article.querySelector("button.maps_add_row")).onclick =
        function () {
            tbody.append(rowTemplate.content.cloneNode(true));
        };
    tbody.onclick = function (ev) {
        var button = /** @type {Element} */ (ev.target).closest(".maps_delete_row");
        if (button) {
            button.closest("tr").remove();
        }
    };
    textarea.form.onsubmit = function () {
        var markers = /** @type {Marker[]} */ ([]);
        tbody.querySelectorAll("tr").forEach(function (row) {
            var marker = /** @type {Marker} */ ({});
            /** @type {NodeListOf<HTMLInputElement|HTMLTextAreaElement>} */ (
                row.querySelectorAll("[name]")
            ).forEach(function (control) {
                if (control.type !== "checkbox") {
                    marker[control.name] = control.value;
                } else {
                    marker[control.name] = /** @type {HTMLInputElement} */ (control).checked;
                }
            });
            markers.push(marker);
        });
        textarea.value = JSON.stringify(markers);
        /** @type {NodeListOf<HTMLInputElement|HTMLTextAreaElement>} */ (
            tbody.querySelectorAll("input, textarea")
        ).forEach(function (el) {
            el.name = "";
        });
    };
});
