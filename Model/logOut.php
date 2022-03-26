<?php
	require_once ('../Model/config.php');
	require_once ('../Model/db.php');

	switch ($_POST['action']){
		case "logOut":
			$object = new Database();
			$object->disconnect();
			echo json_encode(array("status" => 'success', "isConnected" => false));
			break;
		default:
			break;
	}