<?php
	require_once('db.php');
	require_once('config.php');

	switch ($_POST['action']) {
		case "other_profile":
			$username = $_GET['user'];

			$object = new Database();
			$get_user_datas = $object->other_profile($username);

			if ($get_user_datas != NULL) {
				echo json_encode(array("status" => 'Profile success',
					"datas" => $get_user_datas));
			} else {
				header("HTTP/1.0 404 Not Found");
				die(json_encode(array("status" => 'error')));
			}
			break;
		case "other_user_followers":
			$username = $_GET['user'];
			$object = new Database();
			$followers = $object->get_followers_of_other_user($username);
			break;
		case "other_user_followed":
			$username = $_GET['user'];
			$object = new Database();
			$followed = $object->get_follow_of_other_user($username);
			break;
		case "follow_user":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$id = $_SESSION['currentUser'];
			$username = $_GET['user'];
			$object = new Database();
			$follow = $object->follow_user($id, $username);
			break;
		default:
			break;
	}