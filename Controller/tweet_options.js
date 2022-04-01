/////// COMMENT FUNCTION ///////

$(document).on('click', '.comment_tweet', function(){ 
    let blockTweet = $(this).parent().parent()[0];
    let tweetId = $(blockTweet).find('.tweet_content').attr('id');
    $('.tweet-text').empty();
    limit = 140;
    $('.limit-char').css('color', 'white');
    $('.limit-char-tweet').html('');
    $.ajax({
        type: 'post',
        url: "Model/get_tweet_comment.php",
        data: {
            id: tweetId,
        },
        dataType: 'json',
    }).done(function (tweet) {
        let tweetPic;
        if (tweet.originalTweet[0].url_picture == null || tweet.originalTweet[0].url_picture == ''){
            tweetPic = "";
        }
        else {
            tweetPic = '<p>' + tweet.originalTweet[0].url_picture + '</p>';
        }
        $('.original-tweet').append('<div class="tweet">' +
                '<img class="tweet_profile_pic" src="' + tweet.originalTweet[0].picture_url + '" alt="' + tweet.originalTweet[0].username + '_profile_pic">' +
                '<div class="block-tweet"><div class="block-name-date-tweet"><p class="user_name">' + tweet.originalTweet[0].firstname + ' ' + tweet.originalTweet[0].lastname + '</p><p class="at_name"> @' + tweet.originalTweet[0].username + '</p>' +
                '<p class="dot-in-between">' + ' • ' + '</p>' +
                '<p class="tweet_date">' + timeSince(new Date(Date.parse(tweet.originalTweet[0].tweet_date))) + '</p> </div>' +
                '<p class="tweet_content" id="'+ tweet.originalTweet[0].id +'">' + tweet.originalTweet[0].message + '</p>' +
                tweetPic +
                '</div></div>');
        $('.current-user-pic img').attr('src', tweet.profilePic.picture_url);
        $('.comment-tweet-modal').css('display', 'flex');
    })
}); 

/////// MAKE COMMENT MODAL DISSAPPEAR ///////

$(document).on('click', function(e) {
    if ($('.comment-tweet-modal').is(":visible")){
        if ($(e.target).attr('src') != null || $(e.target).attr('src') != undefined){
            if($(e.target).attr('src').indexOf('giphy.gif') != -1){
                return false;
            }
        }
        else if (!$(e.target).closest('.comment-tweet-modal').length > 0) {
            $("original-tweet").empty();
            $("comment-text").empty();
            $('.comment-tweet-modal').hide();
            $('#imported-img-comment').attr('src', '');
            $('#comment-image').hide();
            $('#gif-window-comment').hide();
            $("gifs").empty();
            limit = 140;
            $('.limit-char').css('color', 'white');
            $('.limit-char-tweet').html('');
            $('.limit-char-comment').html('');
        }
    };
});

$('.remove-comment-modal').on('click', function(){
    $('.comment-tweet-modal').css('display', 'none');
    $('.original-tweet').empty();
    $("comment-text").empty();
    $('#imported-img-comment').attr('src', '');
    $('#comment-image').hide();
    $('#gif-window-comment').hide();
    $("gifs").empty();
    limit = 140;
    $('.limit-char').css('color', 'white');
    $('.limit-char-tweet').html('');
    $('.limit-char-comment').html('');
});

/////// RETWEET FUNCTION ///////

$(document).on('click', '.retweet_tweet', function(){ 
    let blockTweet = $(this).parent().parent()[0];
    let tweetContent = $(blockTweet).find('.tweet_content');
    let tweetId = $(blockTweet).find('.tweet_content').attr('id');
    if (tweetContent.hasClass('comment_content')){
        $.ajax({
            type: 'post',
            data: {
                id: tweetId,
            },
            url: "Model/retweet-comment-option.php",
        }).done(function (tweets) {
            console.log(tweets);
        });
    }
    else {
        $.ajax({
            type: 'post',
            data: {
                id: tweetId,
            },
            url: "Model/retweet-option.php",
        }).done(function (tweets) {
            console.log(tweets);
        });
    }
}); 

/////// LIKE FUNCTION ///////

$(document).on('click', '.like_tweet', function(){ 
    let blockTweet = $(this).parent().parent()[0];
    let tweetContent = $(blockTweet).find('.tweet_content');
    let tweetId = $(blockTweet).find('.tweet_content').attr('id');
    if (tweetContent.hasClass('comment_content')){
        $.ajax({
            type: 'post',
            data: {
                id: tweetId,
            },
            url: "Model/like-comment-option.php",
        }).done(function (tweets) {
            console.log(tweets);
        });
    }
    else {
        $.ajax({
            type: 'post',
            data: {
                id: tweetId,
            },
            url: "Model/like-option.php",
        }).done(function (tweets) {
            console.log(tweets);
        });
    }
}); 

/////// A IMPLEMENTER ///////

$(document).on('click', '.share_tweet', function(){ 
    console.log(this);
}); 