function userGridPaginator(_page) {
    //location.replace('?page=AdminModule:UserAdmin:list&grid_page=' + page);
    $.get("app/ajax/UserAdmin.php", {
        action: "ajaxList",
        page: _page
    }, async function(data) {
        $("#tablebuilder-table").html(data);
    });
}