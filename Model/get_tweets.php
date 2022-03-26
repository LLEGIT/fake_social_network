<?php
require 'db.php';
require 'config.php';

$conn = new Database();
$get_tweets = $conn->get_tweets();
?>