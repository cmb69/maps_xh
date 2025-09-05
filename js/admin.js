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

document.querySelectorAll("article.maps_edit").forEach(function (article) {
    let textarea = /** @type {HTMLTextAreaElement} */ (
        document.querySelector("textarea[name=markers]")
    );
    textarea.parentElement.style.display = "none";
    let form = textarea.form;
    form.querySelector(".maps_controls").insertAdjacentHTML(
        "beforebegin",
        /** @type {HTMLScriptElement} */ (article.querySelector("script.maps_table_template")).text
    );
    let table = article.querySelector("table");
    let tbody = table.querySelector("tbody");
    let deleteRowButton = table.querySelector(".maps_delete_row");
    tbody.querySelectorAll("tr td:last-child").forEach(function (td) {
        td.append(deleteRowButton.cloneNode(true));
    });
    deleteRowButton.remove();
    let addRowButton = /** @type {HTMLButtonElement} */ (
        table.querySelector("button.maps_add_row")
    );
    addRowButton.onclick = function () {
        tbody.insertAdjacentHTML(
            "beforeend",
            /** @type {HTMLScriptElement} */ (article.querySelector("script.maps_row_template"))
                .text
        );
        tbody.querySelector("tr:last-child td:last-child").append(deleteRowButton.cloneNode(true));
    };
    tbody.onclick = function (ev) {
        let button = /** @type {Element} */ (ev.target).closest(".maps_delete_row");
        if (button) {
            button.parentElement.parentElement.remove();
        }
    };
    form.onsubmit = function () {
        let form = document.createElement("form");
        form.append(table.cloneNode(true));
        let markers = Array.from(new FormData(form)).reduce(function (acc, pair) {
            let [key, val] = pair;
            if (key === "latitude") {
                acc.push({});
            }
            let marker = acc[acc.length - 1];
            marker[key] = val.toString();
            return acc;
        }, []);
        textarea.value = JSON.stringify(markers);
        /** @type {NodeListOf<HTMLInputElement | HTMLTextAreaElement>} */ (
            table.querySelectorAll("input, textarea")
        ).forEach((el) => (el.name = ""));
    };
});
