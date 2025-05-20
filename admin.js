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
