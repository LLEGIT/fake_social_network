$(document).on('click', '.tweet', function(e){ 
    if ($(e.target).is('i, li')){
        return false;
    }
    else {
        let tweetContent = $(this).find('.tweet_content');
        if(tweetContent.hasClass('comment_content')){
            return false;
        }
        else {
            let tweetId = $(this).find('.tweet_content').attr('id');
            window.location.href = "../View/tweet.html?id=" + tweetId;
        }
    }
}); 