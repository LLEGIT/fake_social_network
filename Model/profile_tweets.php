<?php
	require 'db.php';
	require 'config.php';

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	$id = $_SESSION['currentUser'];
	$conn = new Database();
	$get_tweets = $conn->get_tweets_of_specific_user($id);
?>