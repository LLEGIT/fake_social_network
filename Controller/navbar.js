$(document).on('click', '#homeIcon', function(){ 
    window.location.href = "../View/home.html";
}); 

$(document).on('click', '#explore', function(){ 
    $("main").empty()
    $.ajax({
        method: "GET",
        url: "./../Model/getHashtag.php",
        dataType: "json",
    }).done(function(hashtags) {
        hashtags.forEach(hashtag => {
            $("main").append("<p><a class='hashtag'>" + hashtag.substring(hashtag.indexOf(".") + 1) + "</a><br>" + hashtag.substring(0, hashtag.indexOf(".")) + " tweets</p>")
        })
    })
}); 

$(document).on('click', '#notificationsNav', function(){ 
    console.log(this);
}); 

$(document).on('click', '#messagesIcon', function(){ 
    window.location.href = "../View/message.html";
}); 

$(document).on('click', '#bookmarksNav', function(){ 
    console.log(this);
}); 

$(document).on('click', '#listsNav', function(){ 
    console.log(this);
}); 

$(document).on('click', '#profileNav', function(){ 
    window.location.href = "../View/profile.html";
}); 

$(document).on('click', '#moreNav', function(){ 
    console.log(this);
}); 