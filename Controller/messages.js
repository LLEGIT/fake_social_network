$(document).ready(function () {

    $.ajax({
        method: "GET",
        url: "Model/isConnected.php",
        dataType: "json",
    }).done(function (data) {
        let idUser = data["connected"]
        //Fetching users dms
        $.ajax({
            url: "Model/getMessages.php",
            method: "POST",
            data: { "idUser": idUser },
            dataType: "json",
            success: function (data) {
                for (i = 0; i < data.length; i++) {
                    let img = document.createElement("img")
                    img.src = data[i][0]['picture_url']
                    let li = document.createElement("li")
                    $(li).attr("id", `${data[i][0]['id_sender']}`)
                    $(li).toggleClass("block-message")
                    $(li).append(`<img class="tweet_profile_pic" src='${data[i][0]['picture_url']}' alt='PP'><div class="block-tweet block-message"><div class="block-name-date-tweet"><p class='user_name'>` + data[i][0]['firstname'] + " " + data[i][0]['lastname'] + "<a href='user_profile.html?user=" + data[i][0]['username'] + "' class='at_name'> @" + data[i][0]['username'] + "</a>" + '<p class="dot-in-between">' + ' • ' + '</p>' + `${timeSince(new Date(Date.parse(data[i][0]['sent_date'])))}</p></div><p class="messageContent">${data[i][0]['message']}</p></div>`)
                    $("main ul").append(li)
                    $("main ul li").css("display", "flex")
                    $("main ul li").css("flex-direction", "row")
                    $("main ul li").css("justify-content", "center")
                }
                //When clicking on a message
                $("main ul li").on("click", function () {
                    let idSender = $(this).attr("id")
                    let idUser = data[0][0]['id_receiver']
                    $.ajax({
                        method: "post",
                        url: "Model/get_conversation.php",
                        data: { "idSender": idSender, "idUser": idUser },
                        dataType: 'json',
                        success: function (data) {
                            $("convContainer").empty()
                            if (data['sender'].length > 0 || data['receiver'].length > 0) {
                                for (i = 0; i < Math.max(data['sender'].length, data['receiver'].length); i++) {
                                    console.log(data.sender[i].message + data.receiver[i].message)
                                    if (typeof(data.sender[i]) == 'undefined') {
                                        data.sender[i].message = ""
                                    }
                                    else if (typeof(data.receiver[i]) == 'undefined') {
                                        data.receiver[i].message = ""
                                    }
                                    $("convContainer").append(`<span class='notUserMsg'>${data.sender[i].message}</span><br><span class='userMsg'>${data.receiver[i].message}</span><br>`)
                                }
                            }        
                            $("post-tweet").on("click", function () {
                                $("fake-textarea").css("border", "none")
                                if ($("fake-textarea").text() === "") {
                                    $("fake-textarea").css("border", "1px solid red")
                                }
                                else {
                                    let messageContent = $(".fake-textarea").text()
                                    $.ajax({
                                        method: "POST",
                                        url: "Model/send_message.php",
                                        data: {
                                            "idUser": idUser,
                                            "idReceiver": idSender,
                                            "message": messageContent,
                                        },
                                        success: function (data) {
                                            window.location.reload()
                                        }
                                    })
                                }
                            })
                        },
                        error: function (data) {
                            console.log(data)
                            window.location.replace("index.html");
                        }
                    })
                })
            },
        })
    })
    function timeSince(date) {

        let seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) {
            return Math.floor(interval) + " years";
        }
        interval = seconds / 2592000;
        if (interval > 1) {
            return Math.floor(interval) + " months";
        }
        interval = seconds / 86400;
        if (interval > 1) {
            return Math.floor(interval) + " days";
        }
        interval = seconds / 3600;
        if (interval > 1) {
            return Math.floor(interval) + " hours";
        }
        interval = seconds / 60;
        if (interval > 1) {
            return Math.floor(interval) + " minutes";
        }
        return Math.floor(seconds) + " seconds";
    }
})

