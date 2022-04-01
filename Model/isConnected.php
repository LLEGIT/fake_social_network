<?php
require_once "config.php";
require_once "db.php";

$isConnected = new Database();
if ($isConnected->is_connected() === true) {
    echo json_encode(["connected" => $_SESSION['currentUser']]);
}
else {
    die(json_encode(["connected" => "false"]));
}
