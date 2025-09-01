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

const textarea = document.querySelector("textarea[name=markers]");
textarea.parentElement.style.display = "none";
const form = textarea.form;
let script = form.querySelector("script.maps_table_template");
script.insertAdjacentHTML("beforebegin", script.text);
const table = form.querySelector("table");
let tbody = table.querySelector("tbody");
const deleteButton = form.querySelector(".maps_delete_row");
tbody.querySelectorAll("tr td:last-child").forEach(function (td) {
    td.append(deleteButton.cloneNode(true));
});
deleteButton.remove();
const button = form.querySelector(".maps_add_row");
button.onclick = () => {
    let script = form.querySelector("script.maps_row_template");
    tbody.insertAdjacentHTML("beforeend", script.text);
    tbody.querySelector("tr:last-child td:last-child").append(deleteButton.cloneNode(true));
}
tbody.onclick = function (ev) {
    let button = ev.target.closest(".maps_delete_row");
    if (button) {
        var tr = button.parentElement.parentElement;
        tr.remove();
    }
};
form.onsubmit = () => {
    var markers = [];
    for (var i = 1; i < table.rows.length; i++) {
        var row = table.rows[i];
        var marker = {
            "latitude": row.cells[0].querySelector("input").value,
            "longitude": row.cells[1].querySelector("input").value,
            "info": row.cells[2].querySelector("textarea").value,
            "show": row.cells[3].querySelector("input").checked
        };
        markers.push(marker);
    }
    textarea.value = JSON.stringify(markers);
};
