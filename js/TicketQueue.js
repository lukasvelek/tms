function loadTicketQueueWidget1() {
    $("#widget1_content").html("<img src='img/loading.gif' width='32px'>");
    $("#widget1_content").attr("style", "text-align: center");

    $.get(
        "app/ajax/TicketQueue.php",
        {
            action: "loadWidget1"
        },
        async function(data) {
            const obj = JSON.parse(data);

            $("#widget1_content").html(obj.content);
            $("#widget1_content").removeAttr("style");
        }
    );
}