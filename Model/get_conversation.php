<?php
require_once "config.php";
require_once "db.php";

$get = new Database();
echo json_encode($get->getConversation($_POST['idSender'], $_POST['idUser']));
