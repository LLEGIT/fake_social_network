<?php
require_once "./config.php";
require_once "./db.php";

$getMessage = new Database();
echo json_encode($getMessage->getLastMessage($_POST["idUser"]));
