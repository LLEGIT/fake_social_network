$(document).ready(function () {

    function getFormData($form) {
        let unindexed_array = $form.serializeArray();
        let indexed_array = {};

        $.map(unindexed_array, function (n, i) {
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }

    // EDIT PROFILE MODAL

    $("#edit-profile-btn").click(function () {
        console.log("EDIT PROFILE CLICK");
        $("#edit-profile-modal").css("display", "flex");
    });

    $("#edit-profile-close").click(function () {
        $("#edit-profile-modal").css("display", "none");
    })

    // DISPLAY USER DATA
    $.ajax({
        url: "../Model/users-functions.php",
        type: "post",
        dataType: 'json',
        data: {
            action: "edit-profile-display",
        },
        success: function (result) {
            console.log('===== SUCCESS =====');
            // IMAGES
            $('#background_url').val(result['datas']['background_url']);
            $('#profile-picture').val(result['datas']['picture_url']);
            // GENERALS DATAS
            $('#username').val(result['datas']['username']);
            $('#mail').val(result['datas']['email']);
            $('#bio').val(result['datas']['bio']);
            $('#location').val(result['datas']['location']);
            $('#birthdate').val(result['datas']['birthdate']);
            // PASSWORDS
        },
        error: function (jqxhr, exception) {
            console.log('===== ERROR GETTING USER DATAS FOR PROFILE =====');
            console.log(jqxhr.responseText);
            console.log(exception.responseText);
        },
    });

    // EDITION FORM

    $("#edit-profile-form").on("submit", function (e) {
        e.preventDefault();
        let form = $(this);

        // GET THE EDITED DATAS
        $.ajax({
            url: "../Model/users-functions.php",
            type: "post",
            dataType: 'json',
            data: {
                action: "edit-profile-update",
                data: getFormData(form),
            },
            success: function(data) {
                let dataInt = parseInt(data);
                console.log(dataInt);
                console.log(data);
                if (dataInt === 0) {
                    console.log('===== SUCCESS =====');
                    window.location.reload();
                }
                else if (dataInt === 1) {
                    // WRONG PASSWORD
                    $("#pwdNotCorrespondMsg").css("display", "block")
                    $("#edit-profile-form input[type=password]").css("border", "3px solid red")
                }
                else if (dataInt === 2) {
                    // MAIL ALREADY EXISTS
                    $("#mailExistsMsg").css("display", "block")
                    $("#edit-profile-form #mail").css("border", "3px solid red")
                }
                else if (dataInt === 3) {
                    // USERNAME TAKEN
                    $("#usernameExistsMsg").css("display", "block")
                    $("#edit-profile-form #username").css("border", "3px solid red")
                    console.log(data)
                }
                else {
                    $("#usernameExistsMsg, #mailExistsMsg").css("display", "block")
                    $("#mail, #username").css("border", "3px solid red")
                }
            },
            error: function (jqxhr, exception) {
                console.log('===== ERROR WHILE EDITING =====');
                console.log(jqxhr.responseText);
                window.location.reload()
            },
        });
    })
});