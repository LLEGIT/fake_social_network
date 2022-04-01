<?php
require_once "config.php";
require_once "db.php";

session_start();

$userId = $_SESSION['currentUser'];
$tweetId = $_GET['id'];

$conn = new Database();
$get_selected_tweet = $conn->get_selected_tweet_comments($tweetId, $userId);
?>