<?php
require 'db.php';
require 'config.php';

session_start();

$id = $_POST['id'];
$userId = $_SESSION['currentUser'];

$conn = new Database();
$get_tweets = $conn->like_comment($id, $userId);
?>