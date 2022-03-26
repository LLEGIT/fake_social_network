<?php
	require_once('db.php');
	require_once('config.php');

	switch ($_POST['action']) {
		case "profile":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$id = $_SESSION['currentUser'];
			$object = new Database();
			$get_user_datas = $object->user_profile_display($id);

			if ($get_user_datas != NULL) {
				echo json_encode(array("status" => 'Profile success',
					"datas" => $get_user_datas));
			} else {
				header("HTTP/1.0 404 Not Found");
				die(json_encode(array("status" => 'error')));
			}
			break;
		case "edit-profile-display":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$id = $_SESSION['currentUser'];
			$object = new Database();
			$display_user_datas = $object->user_profile_edit_display($id);

			if ($display_user_datas != NULL) {
				echo json_encode(array("status" => 'Profile success',
					"datas" => $display_user_datas));
			} else {
				header("HTTP/1.0 404 Not Found");
				die(json_encode(array("status" => 'error')));
			}
			break;
		case "edit-profile-update":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$id = $_SESSION['currentUser'];
			$object = new Database();
			$update_user_datas = $object->user_profile_update($id, $_POST['data']['background_url'], $_POST['data']['profile-picture'], $_POST['data']['username'], $_POST['data']['mail'], $_POST['data']['bio'], $_POST['data']['location'], $_POST['data']['birthdate'], $_POST['data']['password'], $_POST['data']['newpassword']);

			if ($update_user_datas != NULL) {
				echo json_encode(array("status" => 'Profile success',
					"datas" => $update_user_datas));
			} else {
				header("HTTP/1.0 404 Not Found");
				die(json_encode(array("status" => 'error')));
			}
			break;
		case "user_connected_check":
			$object = new Database();
			$connection_check = $object->is_connected();
			if ($connection_check === true) {
				echo json_encode(array("status" => 'success', "isConnected" => true));
			} else {
				header("HTTP/1.0 404 Not Found");
				die(json_encode(array("status" => 'error', "isConnected" => false)));
			}
			break;
		case "user_followers":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$id = $_SESSION['currentUser'];
			$object = new Database();
			$followers = $object->get_followers_of_specific_user($id);
			break;
		case "user_followed":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$id = $_SESSION['currentUser'];
			$object = new Database();
			$followers = $object->get_follow_of_specific_user($id);
			break;
		case "unable_account":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$id = $_SESSION['currentUser'];
			$object = new Database();
			$object->duplicate_user_account($id);
			$object->unable_user_account($id);
			echo json_encode(array("status" => 'success'));
			break;
		default:
			header("HTTP/1.0 500 Error in the code");
			die(json_encode(array("status" => 'error', 'message' => '')));
	}