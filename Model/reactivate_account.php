<?php
    require 'db.php';
	require 'config.php';

    $userId = $_GET['id'];

    $conn = new Database();
    $reactivate = $conn->reactivate_user($userId);
?>