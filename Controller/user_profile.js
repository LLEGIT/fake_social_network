$(document).ready(function () {
    $.ajax({
        url: "Model/user_profile.php" + window.location.search,
        type: "post",
        dataType: 'json',
        data: {
            action: "other_profile",
        },
        success: function (result) {
            console.log('===== PROFILE - SUCCESS =====');
            // FIX TO TOP
            $('#profile-username').append('<span class="profile-banner-content regular-text">' + result['datas']['username'] + '</p>');
            $('#profile-title-content').append('<span>' + result['datas']['counts_tweet'] + ' Tweets' + '</span>');
            // BANNER
            $('#user-data').append('<div class="profile-banner-div"><img id="banner-pic" class="profile-banner" src="' + result['datas']['background_url'] + '" alt="Banner picture"/></div>');
            // PROFILE PICTURE
            $('#user-data').append('<div class="profile-picture-div"><img id="profile-pic" class="profile-picture" src="' + result['datas']['picture_url'] + '" alt="Profile picture"/></div>');
            // DESCRIBING
            $('#user-data').append('<span class="profile-banner-bold profile-banner-content regular-text">' + result['datas']['firstname'] + ' ' + result['datas']['lastname'] + '</span>');
            $('#user-data').append('<span class="profile-banner-content regular-text">@' + result['datas']['username'] + '</span>');
            $('#user-data').append('<span class="profile-banner-content regular-text">' + result['datas']['bio'] + '</span>');
            $('#user-data').append('<span class="profile-banner-content profile-under-bio regular-text"><i class="fa-solid fa-location-dot"></i>&nbsp' + result['datas']['location'] + '</span>');
            $('#user-data').append('<span class="profile-banner-content profile-under-bio regular-text"><i class="fa-solid fa-cake-candles"></i>&nbsp Born on the ' + result['datas']['birthdate'] + '</span>');
            $('#user-data').append('<span class="profile-banner-content profile-under-bio regular-text"><i class="fa-solid fa-calendar-days"></i>&nbsp Registration on the ' + result['datas']['registered_date'] + '</span>');
            // COUNTERS
            $('#followers-amount').append('<span class="profile-banner-content regular-text">' + result['datas']['followed'] + '</span>');
            $('#followed-amount').append('<span class="profile-banner-content regular-text">' + result['datas']['follower'] + '</span>');
        },
        error: function (jqxhr) {
            console.log('===== ERROR =====');
            console.log(jqxhr.responseText);
            //window.location.replace("index.html");
        },
    });


    // FETCH DATAS
    $.ajax({
        url: "Model/user_profile.php" + window.location.search,
        type: "post",
        dataType: 'json',
        data: {
            action: "other_user_followers",
        },
        success: function (result) {
            console.log('===== FOLLOWERS - SUCCESS =====');
            console.log(result);
            result.forEach(follower => {
                // PROFILE PICTURE + USERNAME
                $('.followers-list').append('<div class="follower-element"><img class="tweet_profile_pic" src="' + follower['picture_url'] + '" alt="Profile picture">' +
                    '<a href="user_profile.html?id_user=' + follower['username'] + 'class="profile-banner-content regular-text">@' + follower['username'] + '</a>' + '</div>');

            })
        },
        error: function (jqxhr) {
            console.log('===== FOLLOWERS - ERROR =====');
            console.log(jqxhr.responseText);
            //window.location.replace("index.html");
        },
    })

    $('#followers-profile-btn').click(function () {
        // OPEN MODAL
        $("#followers-profile-modal").css("display", "flex");

        // CLOSE MODAL
        $("#followers-profile-close").click(function () {
            $("#followers-profile-modal").css("display", "none");
        })
    });

    // FETCH DATAS
    $.ajax({
        url: "Model/user_profile.php" + window.location.search,
        type: "post",
        dataType: 'json',
        data: {
            action: "other_user_followed",
        },
        success: function (result) {
            console.log('===== FOLLOWED - SUCCESS =====');

            result.forEach(followed => {
                console.log(followed);
                // PROFILE PICTURE + USERNAME
                $('.followed-list').append('<div class="follower-element"><img class="tweet_profile_pic" src="' + followed['picture_url'] + '" alt="Profile picture">' +
                    '<a href="user_profile.html?id_user=' + followed['username'] + '" class="profile-banner-content regular-text">@' + followed['username'] + '</a>' + '</div>');

            })
        },
        error: function (jqxhr) {
            console.log('===== FOLLOWED - ERROR =====');
            console.log(jqxhr.responseText);
            //window.location.replace("index.html");
        },
    })

    $('#followed-profile-btn').click(function () {
        // OPEN MODAL
        $("#followed-profile-modal").css("display", "flex");

        // CLOSE MODAL
        $("#followed-profile-close").click(function () {
            $("#followed-profile-modal").css("display", "none");
        })
    });

    // FOLLOW

    $('#follow-profile-btn').click(function (){
        $.ajax({
            url: "Model/user_profile.php" + window.location.search,
            type: "post",
            dataType: 'json',
            data: {
                action: "follow_user",
            },
            success: function (res) {
                console.log('===== FOLLOW - SUCCESS =====');
                console.log(res);
            },
            error: function (jqxhr) {
                console.log('===== FOLLOWED - ERROR =====');
                console.log(jqxhr.responseText);
                //window.location.replace("index.html");
            },
            }
        )
    })
});
