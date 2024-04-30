$("#password").on("change", function() {
    if($("#password").val() != "" && $("#password_again").val() != "" && $("#password_again").val() != $("#password").val()) {
        if($("#password_again").val() != $("#password").val()) {
            $("#password").css("border", "1px solid red");
            $("#password_again").css("border", "1px solid red");
            $("#submit").attr('disabled', true);
        } else {
            $("#password").css("border", "1px solid black");
            $("#password_again").css("border", "1px solid black");
            $("#submit").attr('disabled', false);
        }
    }
});

$("#password_again").on("change", function() {
    if($("#password").val() != "" && $("#password_again").val() != "") {
        if($("#password_again").val() != $("#password").val()) {
            $("#password").css("border", "1px solid red");
            $("#password_again").css("border", "1px solid red");
            $("#submit").attr('disabled', true);
        } else {
            $("#password").css("border", "1px solid black");
            $("#password_again").css("border", "1px solid black");
            $("#submit").attr('disabled', false);
        }
    }
});