<?php
require 'db.php';
require 'config.php';

session_start();

$tweetId = $_POST['tweet_id'];

$conn = new Database();
$get_tweet_comment = $conn->get_comment_number($tweetId);
?>