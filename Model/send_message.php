<?php
require_once "config.php";
require_once "db.php";

$sendMsg = new Database();
$sendMsg->writeMessage($_POST['idUser'], $_POST['idReceiver'], $_POST['message']);