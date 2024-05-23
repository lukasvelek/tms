function loadTicketQueueWidget1() {
    $.get(
        "app/ajax/TicketQueue.php",
        {
            action: "loadWidget1"
        },
        async function(data) {
            const obj = JSON.parse(data);

            $("#widget1_content").html(obj.content);
        }
    );
}