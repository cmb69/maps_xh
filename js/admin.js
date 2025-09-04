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

/* jshint browser:true,esversion:6,module:true,varstmt:true */
// @ts-check

let article = document.querySelector("article.maps_edit");
let textarea = /** @type {HTMLTextAreaElement} */ (
    document.querySelector("textarea[name=markers]")
);
textarea.parentElement.style.display = "none";
let form = textarea.form;
let script = /** @type {HTMLScriptElement} */ (article.querySelector("script.maps_table_template"));
form.querySelector(".maps_controls").insertAdjacentHTML("beforebegin", script.text);
let table = article.querySelector("table");
let tbody = table.querySelector("tbody");
let deleteButton = table.querySelector(".maps_delete_row");
tbody.querySelectorAll("tr td:last-child").forEach(function (td) {
    td.append(deleteButton.cloneNode(true));
});
deleteButton.remove();
let button = /** @type {HTMLButtonElement} */ (table.querySelector("button.maps_add_row"));
button.onclick = () => {
    let script = /** @type {HTMLScriptElement} */ (
        article.querySelector("script.maps_row_template")
    );
    tbody.insertAdjacentHTML("beforeend", script.text);
    tbody.querySelector("tr:last-child td:last-child").append(deleteButton.cloneNode(true));
};
tbody.onclick = function (ev) {
    let button = ev.target.closest(".maps_delete_row");
    if (button) {
        let tr = button.parentElement.parentElement;
        tr.remove();
    }
};
form.onsubmit = () => {
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
