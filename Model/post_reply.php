<?php
require 'db.php';
require 'config.php';

session_start();

$reply = $_POST['reply'];
$reply_pic = $_POST['replypic'];
$tweetId = $_POST['tweetId'];

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

if($reply == ""){
    echo null;
    return false;
}
else {
    if(str_contains($reply, "'")){
        $tmp = str_replace ("'","''", $reply);
        $reply = $tmp;
    }
    $conn = new Database();
    $post_reply = $conn->post_reply($userId, $reply, $reply_pic, $tweetId);
}
?>