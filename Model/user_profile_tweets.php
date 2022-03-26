<?php
	require 'db.php';
	require 'config.php';

	$username = $_GET['user'];

	$conn = new Database();
	$get_specific_user_tweets = $conn->get_tweets_of_other_user($username);
?>