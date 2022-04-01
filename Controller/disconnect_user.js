$(document).ready(function (){
    $("#logout-btn").click(function () {
        $.ajax({
            url: "Model/logOut.php",
            type: "post",
            dataType: 'json',
            data: {
                action: "logOut",
            },
            success: function (result) {
                console.log('===== SUCCESS =====');
                console.log(result);
                window.location.replace("index.html");
            },
            error: function (jqxhr, exception) {
                console.log('===== ERROR =====');
                console.log("ERROR IN SCRIPT LOGOUT");
                window.location.replace("index.html");
            },
        });
    });
})