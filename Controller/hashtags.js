$(document).ready(function() {
    $.ajax({
        type: "get",
        url: "../Model/getHashtag.php",
        dataType: "json",
    }).done(function(res) {
        //To display only five first tweets
        let i = 1
        res.forEach(hashtag => {
            if (i < 5) {
                let li = document.createElement("li")
                let content = "<p class='topHashtags'>" + hashtag.substring(hashtag.indexOf(".") + 1) + "<br>" + hashtag.substring(0, hashtag.indexOf(".")) + " tweets<p>"
                $(li).append(content)
                $(".trends ul").append(li)
            }
            i++
        })
    })
})