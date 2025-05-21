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
const table = form.querySelector("table");
table.style.display = "table";
const deleteButton = table.rows[table.rows.length - 1].cells[4].firstChild;
for (var i = 1; i < table.rows.length - 1; i++) {
    table.rows[i].cells[4].appendChild(deleteButton.cloneNode(true));
}
const button = form.querySelector(".maps_add_row");
button.onclick = () => {
    var clone = table.rows[table.rows.length - 1].cloneNode(true);
    table.tBodies[0].appendChild(clone);
    clone.querySelector(".maps_delete_row").onclick = event => {
        var tr = event.currentTarget.parentElement.parentElement;
        tr.parentElement.removeChild(tr);
    }
}
form.querySelectorAll(".maps_delete_row").forEach(button => {
    button.onclick = () => {
        var tr = button.parentElement.parentElement;
        tr.parentElement.removeChild(tr);
    }
})
form.onsubmit = () => {
    var markers = [];
    for (var i = 1; i < table.rows.length - 1; i++) {
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
