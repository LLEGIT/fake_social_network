

// Script called when connection is NOT required

window.onload = function () {
    $.ajax({
        url: "../model/users-functions.php",
        type: "post",
        dataType: 'json',
        data: {
            action: "user_connected_check",
            mustBeConnected: false,
        },
        success: function (result) {
            console.log('===== SUCCESS =====');
            console.log(result);
            if (result['isConnected'] == true) {
                }
        },
        error: function (jqXHR, exception) {
            console.log('===== ERROR =====');
            console.log("ERROR IN SCRIPT CONNECTED - FALSE");
            console.log(jqXHR.responseText);
            $('#logout-btn').val("LOG IN");
            $('#logout-btn').click(function (){
                window.location.replace("index.html");
            })
            $('.tweet-textarea').css("display", "none");

        },
    });
}