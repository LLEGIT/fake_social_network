// Script called when connection is required

window.onload = function () {
    $.ajax({
        url: "../model/users-functions.php",
        type: "post",
        dataType: 'json',
        data: {
            action: "user_connected_check",
            mustBeConnected: true,
        },
        success: function (result) {
            console.log('===== USER CONNECTED =====');
            console.log(result);
        },
        error: function (jqXHR, exception) {
            console.log('===== ERROR =====');
            console.log("ERROR IN SCRIPT CONNECTED - TRUE");
            window.location.replace("index.html");
            console.log(jqXHR.responseText);
        },
    })
}