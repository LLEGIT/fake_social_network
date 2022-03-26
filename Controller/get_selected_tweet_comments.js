$(document).ready(function(){
    let url = window.location.href;
    let id = url.substring(url.lastIndexOf('?id=')+4);
    let repliedTo = "";

    let tweet_pic = '';
    let tweet_rt = '';
    let tweet_likes = '';
    let comment_pic = '';
    let comment_rt = '';
    let comment_likes = '';

    $.ajax({
        type: 'get',
        url: "../Model/get_selected_tweet_comments.php",
        data: {
            id: id,
        },
        dataType: 'json',
    }).done(function (datas) {
        $('.current-user-pic img').attr('src', datas.currentuser[0].picture_url);
        
        datas.tweet.forEach(tweet => {
            let regex1 = /^#\w+/
            let regex2 = /^@\w+/
            let split = tweet.message.split(" ")
            let tweetArr = []
            split.forEach(word => {
                if (word.match(regex1)) {
                    if (word.includes("/")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf("/")) + "</a>" + word.substring(word.indexOf("/"))
                    }
                    else if (word.includes(".")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf(".")) + "</a>" + word.substring(word.indexOf("."))
                    }
                    else if (word.includes("-")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf("-")) + "</a>" + word.substring(word.indexOf("-"))
                    }
                    else if (word.includes("_")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf("_")) + "</a>" + word.substring(word.indexOf("/"))
                    }
                    else {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word + "</a>"
                    }
                }
                else if (word.match(regex2)) {
                    word = "<a class='tagged' href='taggedPerson.html'>" + word + "</a>"
                }
                tweetArr.push(word)
            })
            tweet.message = tweetArr.join(" ")

            if (tweet.url_picture == '' || tweet.url_picture == null) {
                tweet_pic = '';
            }
            else {
                tweet_pic = '<img class="tweet_pic" src="' + tweet.url_picture + '" alt="' + tweet.username + '_tweet_pic">';
            }

            if (tweet.retweet != 0 && tweet.retweet == null) {
                tweet_rt = tweet.retweet;
            }
            else {
                tweet_rt = '';
            }

            if (tweet.likes != 0 && tweet.likes != null) {
                tweet_likes = tweet.likes;
            }
            else {
                tweet_likes = '';
            }
            
            repliedTo = tweet.username;

            $('.selected-tweet').append('<div class="tweet">' +
                '<img class="tweet_profile_pic" src="' + tweet.picture_url + '" alt="' + tweet.username + '_profile_pic">' +
                '<div class="block-tweet"><div class="block-name-date-tweet"><p class="user_name">' + tweet.firstname + ' ' + tweet.lastname + '</p><p class="at_name"> @' + tweet.username + '</p>' +
                '<p class="dot-in-between">' + ' • ' + '</p>' +
                '<p class="tweet_date">' + timeSince(new Date(Date.parse(tweet.tweet_date))) + '</p> </div>' +
                '<p class="tweet_content" id="'+ tweet.id +'">' + tweet.message + '</p>' +
                tweet_pic +
                '<ul><li class="comment_tweet comment_id_'+ tweet.id + '"><i class="fa-solid fa-comment fa-sm"></i><p></p></li>' +
                '<li class="retweet_tweet"><i class="fa-solid fa-repeat fa-sm"></i><p>' + tweet_rt + '</p></li>' +
                '<li class="like_tweet"><i class="fa-solid fa-heart fa-sm"></i><p>' + tweet_likes + '</p></li>' +
                '<li class="share_tweet"><i class="fa-solid fa-arrow-up-from-bracket fa-sm"></i></li></ul></div></div>')
                $.ajax({
                    type: 'post',
                    url: "../Model/get_comment_number.php",
                    data: {
                        tweet_id: tweet.id,
                    },
                    dataType: 'json',
                }).done(function (comments) {
                    let comment = '.comment_id_'+tweet.id+' p';
                    if (comments[0].nb != 0){
                        $(comment).append(comments[0].nb);
                    }
                    else {
                        return false;
                    }
                });
        })

        datas.comments.forEach(comment => {
            let regex1 = /^#\w+/
            let regex2 = /^@\w+/
            let split = comment.message.split(" ")
            let commentArr = []
            split.forEach(word => {
                if (word.match(regex1)) {
                    if (word.includes("/")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf("/")) + "</a>" + word.substring(word.indexOf("/"))
                    }
                    else if (word.includes(".")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf(".")) + "</a>" + word.substring(word.indexOf("."))
                    }
                    else if (word.includes("-")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf("-")) + "</a>" + word.substring(word.indexOf("-"))
                    }
                    else if (word.includes("_")) {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word.substring(0, word.indexOf("_")) + "</a>" + word.substring(word.indexOf("/"))
                    }
                    else {
                        word = "<a class='hashtag' href='searchByHashtag.html'>" + word + "</a>"
                    }
                }
                else if (word.match(regex2)) {
                    word = "<a class='tagged' href='taggedPerson.html'>" + word + "</a>"
                }
                commentArr.push(word)
            })
            comment.message = commentArr.join(" ")

            if (comment.url_picture == '' || comment.url_picture == null) {
                comment_pic = '';
            }
            else {
                comment_pic = '<img class="comment_pic" src="' + comment.url_picture + '" alt="' + comment.username + '_tweet_pic">';
            }

            if (comment.rt != 0 && comment.rt != null) {
                comment_rt = comment.rt;
            }
            else {
                comment_rt = '';
            }

            if (comment.likes != 0 && comment.likes != null) {
                comment_likes = comment.likes;
            }
            else {
                comment_likes = '';
            }
            


            $('.comments').append('<div class="tweet">' +
                '<img class="tweet_profile_pic" src="' + comment.picture_url + '" alt="' + comment.username + '_profile_pic">' +
                '<div class="block-tweet"><div class="block-name-date-tweet"><p class="user_name">' + comment.firstname + ' ' + comment.lastname + '</p><p class="at_name"> @' + comment.username + '</p>' +
                '<p class="dot-in-between">' + ' • ' + '</p>' +
                '<p class="tweet_date">' + timeSince(new Date(Date.parse(comment.date_tweet))) + '</p> </div>' +
                '<p class="replying_to"> Replying to <span class="at-class">@' + repliedTo + '</span></p>' +
                '<p class="tweet_content comment_content" id="'+ comment.id +'">' + comment.message + '</p>' +
                comment_pic +
                '<ul><li class="comment_tweet"><i class="fa-solid fa-comment fa-sm"></i><p></p></li>' +
                '<li class="retweet_tweet"><i class="fa-solid fa-repeat fa-sm"></i><p>' + comment_rt + '</p></li>' +
                '<li class="like_tweet"><i class="fa-solid fa-heart fa-sm"></i><p>' + comment_likes + '</p></li>' +
                '<li class="share_tweet"><i class="fa-solid fa-arrow-up-from-bracket fa-sm"></i></li></ul></div></div>')
        })
    });
});

$(document).on('click', '.go-back-home', function(){ 
    window.location.href = "../View/home.html";
}); 

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