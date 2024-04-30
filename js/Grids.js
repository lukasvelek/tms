function userGridPaginator(_page) {
    $.get("app/ajax/UserAdmin.php", {
        action: "ajaxList",
        page: _page
    }, async function(data) {
        const obj = JSON.parse(data);
        $("#table").html(obj.table);
        $("#table-controls").html(obj.controls);
    });
}