<?php
require 'db.php';
require 'config.php';

session_start();

$tweet = $_POST['tweet'];
$tweet_pic = $_POST['tweetpic'];

if(isset($_SESSION['currentUser'])){
    if($_SESSION['currentUser'] != ""){
        $userId = $_SESSION['currentUser'];
    }
    else {
        $userId = '';
    }
}
else {
    echo null;
    return false;
}

if($tweet == ""){
    echo null;
    return false;
}
else {
    if(str_contains($tweet, "'")){
        $tmp = str_replace ("'","''", $tweet);
        $tweet = $tmp;
    }
    $conn = new Database();
    $post_tweet = $conn->post_tweet($userId, $tweet, $tweet_pic);
}
?>