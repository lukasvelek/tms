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

function clientGridPaginator(_page) {
    $.get("app/ajax/ClientAdmin.php", {
        action: "ajaxList",
        page: _page
    }, async function(data) {
        const obj = JSON.parse(data);
        $("#table").html(obj.table);
        $("#table-controls").html(obj.controls);
    });
}

function clientUsersGridPaginator(_page, _idClient) {
    $.get("app/ajax/ClientAdmin.php", {
        action: "ajaxUsersList",
        page: _page,
        idClient: _idClient
    }, async function(data) {
        const obj = JSON.parse(data);
        $("#table").html(obj.table);
        $("#table-controls").html(obj.controls);
    })
}

function projectGridPaginator(_page) {
    $.get("app/ajax/ProjectAdmin.php", {
        action: "ajaxProjectList",
        page: _page
    }, async function(data) {
        const obj = JSON.parse(data);
        $("#table").html(obj.table);
        $("#table-controls").html(obj.controls);
    });
}