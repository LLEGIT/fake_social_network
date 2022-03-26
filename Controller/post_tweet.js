/////// LIMIT 140 CHARACTER ///////

let limit = 140;

$('.tweet-text').keyup(function(e){
    let keyCo = e.keyCode || e.charCode;
    if (e.key.length === 1 || keyCo == 8 || keyCo == 46){
        if($('.tweet-text').text() == null || $('.tweet-text').text() == ""){
            limit = 140;
        }
        else {
            limit = 140;
            limit = limit - $('.tweet-text').text().length;
        }
        if(limit<0){
            $(".post-tweet").prop('disabled', true);
            $('.limit-char-tweet').css('color', 'red');
            $(".post-tweet").attr("class", 'tweet-disabled');
        }
        else{
            $('.limit-char-tweet').css('color', 'white');
            $(".tweet-disabled").attr("class", 'post-tweet');
            $(".post-tweet").prop('disabled', false);
        }
        $('.limit-char-tweet').html(limit);
    }
})

$('.comment-text').keyup(function(e){
    let keyCo = e.keyCode || e.charCode;
    if (e.key.length === 1 || keyCo == 8 || keyCo == 46){
        if($('.comment-text').text() == null || $('.comment-text').text() == ""){
            limit = 140;
        }
        else {
            limit = 140;
            limit = limit - $('.comment-text').text().length;
        }
        if(limit<0){
            $(".post-reply").prop('disabled', true);
            $('.limit-char-comment').css('color', 'red');
            $(".post-reply").attr("class", 'tweet-disabled');
        }
        else{
            $('.limit-char-comment').css('color', 'white');
            $(".tweet-disabled").attr("class", 'post-reply');
            $(".post-reply").prop('disabled', false);
        }
        $('.limit-char-comment').html(limit);
    }
})

$('.comment-tweet-text').keyup(function(e){
    let keyCo = e.keyCode || e.charCode;
    if (e.key.length === 1 || keyCo == 8 || keyCo == 46){
        if($('.comment-tweet-text').text() == null || $('.comment-tweet-text').text() == ""){
            limit = 140;
        }
        else {
            limit = 140;
            limit = limit - $('.comment-tweet-text').text().length;
        }
        if(limit<0){
            $(".post-reply").prop('disabled', true);
            $('.limit-char-comment').css('color', 'red');
            $(".post-reply").attr("class", 'tweet-disabled');
        }
        else{
            $('.limit-char-comment').css('color', 'white');
            $(".tweet-disabled").attr("class", 'post-reply');
            $(".post-reply").prop('disabled', false);
        }
        $('.limit-char-comment').html(limit);
    }
})


$('.tweet-text').on('click', function(){
    $('.limit-char-tweet').html(limit);
});

$('.comment-text').on('click', function(){
    $('.limit-char-comment').html(limit);
});

$('.comment-tweet-text').on('click', function(){
    $('.limit-char-comment').html(limit);
});

/////// ADD AND DELETE PICTURE FROM TWEET ///////

let fileOrigin;

$('#add-picture-tweet').on('click', function(e){
    $('#add-pic-tweet').click();
    fileOrigin = 'tweet';
})

$('#add-picture-comment').on('click', function(e){
    $('#add-pic-comment').click();
    fileOrigin = 'comment';
})

$('.tweet-options input[type=file]').change(function (e) {
    uploadFile();
});

$('.comment-options input[type=file]').change(function (e) {
    uploadFile();
});

$('#delete-tweet-image').on('click',function(e){
    let srcfile = $('#imported-img-tweet').attr('src');
    if (srcfile.indexOf('giphy.gif') != -1){
        $('#imported-img-tweet').attr('src', '');
        $('#tweet-image').hide();
    }
    else {
        deleteFile(srcfile);
    }
})

$('#delete-comment-image').on('click',function(e){
    let srcfile = $('#imported-img-comment').attr('src');
    if (srcfile.indexOf('giphy.gif') != -1){
        $('#imported-img-comment').attr('src', '');
        $('#comment-image').hide();
    }
    else {
        deleteFile(srcfile);
    }
})

/////// FONCTION POUR UPLOAD D'IMAGES ///////

let previousPic = "";

function uploadFile() {
    let file;
    if(fileOrigin == 'tweet'){
        file = $('#add-pic-tweet').prop('files')[0];
    }
    else {
        file = $('#add-pic-comment').prop('files')[0];
    }

    if (previousPic != ""){
        deleteFile(previousPic);
    }
    
    if (file == null){
        return false;
    }

    if (file.size > 5000000){
        alert('Your file is too big');
        return false;
    }

    if(file.type.includes("image")) {
        let form_data = new FormData();
        form_data.append('file', file);
        $.ajax({
            url: '../Model/upload_image.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                if(response == 'invalid'){
                    alert("Une erreur est survenue.");
                    return false;
                }
                else {
                    previousPic = response;
                    if (fileOrigin == 'tweet'){
                        $('#imported-img-tweet').attr('src', response);
                        $('#tweet-image').show();
                    }
                    else {
                        $('#imported-img-comment').attr('src', response);
                        $('#comment-image').show();
                    }
                }
            },
            error: function (response) {
                console.log(response);
            }
        });
    }
}

/////// FONCTION POUR SUPPRIMER IMAGE ///////

function deleteFile(srcfile) {

    previousPic = "";

    $.ajax({
        url: '../Model/delete_image.php',
        type: 'post',
        data: {
            path: srcfile,
        },
        success: function (response) {
            if(response == 'invalid'){
                alert("Une erreur est survenue.");
                return false;
            }
            else {
                $('.imported-image').attr('src', '');
                $('.tweet-image').hide();
            }
        },
        error: function (response) {
        }
    });
}

/////// ADD AND DELETE GIFS FROM TWEET ///////

$('#add-gif-tweet').on('click', function(e){
    e.stopPropagation();
    getGiphy(0);
    $('#gif-window-tweet').show();
})

$('#gifs-tweet').on ('click', 'img', function(){
    if ($(this).attr('src').indexOf('giphy.gif') != -1){
        $('#imported-img-tweet').attr('src', $(this).attr('src'));
        $('#tweet-image').show();
        $('.gif-window').hide();
        $(".gifs").empty();
    }
})

/////// ADD AND DELETE GIFS FROM COMMENT ///////

$('#add-gif-comment').on('click', function(e){
    e.stopPropagation();
    getGiphy(0);
    $('#gif-window-comment').show();
})

$('#gifs-comment').on ('click', 'img', function(){
    if ($(this).attr('src').indexOf('giphy.gif') != -1){
        $('#imported-img-comment').attr('src', $(this).attr('src'));
        $('#comment-image').show();
        $('#gif-window-comment').hide();
        $(".gifs").empty();
    }
})

/////// HIDE GIF WINDOW ///////

$(document).on('click', function(e) {
    if ($('.gif-window').is(":visible")){
        if (!$(e.target).closest('.gif-window').length > 0) {
            $(".gifs").empty();
            $('.gif-window').hide();
        }
    };
});

$('.remove-gif-window').on('click', function(e){
    $(".gifs").empty();
    $('.gif-window').hide();
})

let gifSearch;

$('.gif-search').keyup(function(){
    gifSearch = $('.gif-search').val();
    $(".gifs").empty();
    getGiphy(0);
})

/////// A IMPLEMENTER ///////

$('.add-poll').on('click', function(e){
    console.log("ok poll");
})

/////// ADD SMILEY TWEET ///////

$('#add-smiley-tweet').on('click', function(e){
    if($("#emoji-window-tweet").is(':visible')){
        $("#emoji-window-tweet").html('');
        $("#emoji-window-tweet").hide();
    }
    else {
        $("#emoji-window-tweet").show();
        $("#emoji-window-tweet").disMojiPicker();
    }
})

$("#emoji-window-tweet").on('click', 'span', function(e){
    $('.tweet-text').append($(this).html())
    $("#emoji-window-tweet").hide();
});

/////// ADD SMILEY COMMENT ///////

$('#add-smiley-comment').on('click', function(e){
    if($("#emoji-window-comment").is(':visible')){
        $("#emoji-window-comment").html('');
        $("#emoji-window-comment").hide();
    }
    else {
        $("#emoji-window-comment").show();
        $("#emoji-window-comment").disMojiPicker();
    }
})

$("#emoji-window-comment").on('click', 'span', function(e){
    $('.comment-text').append($(this).html())
    $("#emoji-window-comment").hide();
});

/////// A IMPLEMENTER ///////

$('.schedule-tweet').on('click', function(e){
    console.log("ok schedule");
})

$('.add-location').on('click', function(e){
    console.log("ok location");
})

/////// POST TWEET ///////

$('.post-tweet').on('click', function(e){
    let tweet = $('.tweet-text').text();
    let tweet_pic = $('.imported-image').attr('src');

    $.ajax({
        type: 'post',
        url: "../Model/post_tweet.php",
        data: {
            tweet: tweet,
            tweetpic: tweet_pic,
        },
    }).done(function(data) {
        location.reload();
    })
})

/////// POST REPLY ///////

$('.post-reply').on('click', function(e){
    let reply = $('.fake-textarea').text();
    let reply_pic = $('.imported-image').attr('src');

    let blockTweet = $(this).parent().parent().parent().parent().parent()[0];
    let tweetId = $(blockTweet).find('.tweet_content').attr('id');

    $.ajax({
        type: 'post',
        url: "../Model/post_reply.php",
        data: {
            reply: reply,
            replypic: reply_pic,
            tweetId: tweetId,
        },
    }).done(function(data) {
        location.reload();
    })
})

/////// FONCTION GIFS ///////

let offset = 0;
let offsetVal = 0;
let giphyLimit = 25;

function getGiphy (i){
    if (gifSearch == null || gifSearch == ""){
        if (i > 0){
            offsetVal = giphyLimit*i;
        }
        $.ajax({
            url: 'https://api.giphy.com/v1/gifs/trending',
            type: 'GET',
            dataType: 'json',
            data: {api_key:'w5ecCeydx0Pv2Z0Adw36W9tSHP4D3eLy', limit: giphyLimit, offset: offsetVal},
            success: (data) => {
                $.each(data['data'], ( index, value) => {
                    let imageUrl = value['images']['original']['url'];
                    $(".gifs").append("<img width='255' src='"+imageUrl+"' />");
                })
                // increase offset to get further items.
                offset = offset+1;
            }
        })
    }
    else {
        if (i > 0){
            offsetVal = giphyLimit*i;
        }
        $.ajax({
            url: 'https://api.giphy.com/v1/gifs/search',
            type: 'GET',
            dataType: 'json',
            data: {q: gifSearch ,api_key:'w5ecCeydx0Pv2Z0Adw36W9tSHP4D3eLy', limit: giphyLimit, offset: offsetVal},
            success: (data) => {
                $.each(data['data'], ( index, value) => {
                    let imageUrl = value['images']['original']['url'];
                    $(".gifs").append("<img width='255' src='"+imageUrl+"' />");
                })
                // increase offset to get further items.
                offset = offset+1;
            }
        })
    }
}

$('.gif-window').scroll(() => {
    if($('.gif-window').scrollTop() == $('.gif-window')[0].scrollHeight - $('.gif-window').height() - 20) {
        getGiphy(offset);
    }
});