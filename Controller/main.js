$(document).ready(function() {
    //Connection modal
    $("#signIn").on("click", function() {
        $("#signInModal").css("display", "flex")
    })
    $("#contentSignInModal span").on("click", function() {
        $("#signInModal").css("display", "none")
    })

    //Sign up modal
    $("#signUp").on("click", function() {
        $("#signUpModal").css("display", "flex")
    })

    $("#contentSignUpModal #closeModal").on("click", function() {
        $("#signUpModal").css("display", "none")
    })

    //On sign in form submit
    $("#signInForm").on("submit", function(event) {
        event.preventDefault()
        let mailOrUsername = $("#mailOrUsername").val()
        let password = $("#password").val()
        let formData = "mailOrUsername=" + mailOrUsername + "&password=" + password + "&signIn=true"
        //ajax function
        $.ajax({
            type: "POST",
            url: "./../Model/signController.php",
            data: formData,
            cache: false,
            success: function(data) {
                data2 = data.substr(1);
                data1 = data.substr(0, 1)
                dataInt = parseInt(data1)
                if (dataInt === 0) {
                    $("#signInForm p").css("display", "none")
                    $("#signInForm input").css("border","none")
                    $("#wrongMailMsg").css("display", "block")
                    $("#mailOrUsername").css("border", "3px solid red")
                }
                else if (dataInt === 1) {
                    window.location.replace("./home.html")
                }
                else if (dataInt === 2) {
                    $("#signInForm p").css("display", "none")
                    $("#signInForm input").css("border","none")
                    $("#wrongPwdMsg").css("display", "block")
                    $("#password").css("border", "3px solid red")
                }
                else if (dataInt === 3){
                    let answer=confirm('Your account is disabled, would you like to re-enable it?');
                    if(answer){
                        $.ajax({
                            type: "GET",
                            url: "./../Model/reactivate_account.php",
                            data: {
                                id: data2,
                            },
                            dataType: 'text',
                            }).done(function (response) {
                                alert(response);
                                setTimeout(function () {
                                    window.location.replace("./home.html")
                                }, 500);
                            });
                    }
                    else{
                        $.ajax({
                            url: "../Model/logOut.php",
                            type: "post",
                            dataType: 'json',
                            data: {
                                action: "logOut",
                            },
                            }).done(function (response) {
                                alert("Your account will stay disabled.");
                            });
                        
                    }
                }
            }
        })
    })
    //On sign up form submit
    $("#signUpForm").on("submit", function(event) {
        event.preventDefault()
        //ajax function
        $.ajax({
            type: "POST",
            url: "./../Model/signController.php",
            data: $("#signUpForm").serialize(),
            cache: false,
            success: function(data) {
                console.log(data)
                dataInt = parseInt(data)
                if (dataInt === 0) {
                    alert("Your account has been created !")
                    window.location.reload()
                }
                else if (dataInt === 1) {
                    $("#signUpForm p").css("display", "none")
                    $("#signUpForm input").css("border","none")
                    $("#pwdNotCorrespondMsg").css("display", "block")
                    $("#signUpForm input[type=password]").css("border", "3px solid red")
                }
                else if (dataInt === 2) {
                    $("#signUpForm p").css("display", "none")
                    $("#signUpForm input").css("border","none")
                    $("#mailExistsMsg").css("display", "block")
                    $("#signUpForm #mail").css("border", "3px solid red")
                }
                else if (dataInt === 3) {
                    $("#signUpForm p").css("display", "none")
                    $("#signUpForm input").css("border","none")
                    $("#usernameExistsMsg").css("display", "block")
                    $("#signUpForm #username").css("border", "3px solid red")
                    console.log(data)
                }
                else if (dataInt === 4) {
                    $("#signUpForm p").css("display", "none")
                    $("#signUpForm input").css("border","none")
                    $("#usernameExistsMsg, #mailExistsMsg").css("display", "block")
                    $("#mail, #username").css("border", "3px solid red")
                }
                else {
                    console.log("ERROR")
                }
            }
        })
    })
})