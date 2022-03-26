<?php

	class Database {
		private $dbHost = DB_HOST;
		private $dbUser = DB_USER;
		private $dbPass = DB_PASS;
		private $dbName = DB_NAME;
		private $dbHandler;
		private $error;

		// DATABASE CONNECTION
		function __construct() {
			$conn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
			$options = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			);

			try {
				$this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass, $options);
			} catch (PDOException $e) {
				$this->error = $e->getMessage();
				echo $this->error;
			}
		}

		// SIGN IN FUNCTION
		public function signIn($mailOrUsername, $password) {
			// Retrieving password
			$sql1 = "SELECT id, pwd FROM users WHERE email LIKE '$mailOrUsername' OR username LIKE '$mailOrUsername'";
			$check = $this->dbHandler->query($sql1);
			$check->execute();
			$checkRes = $check->fetchAll(PDO::FETCH_ASSOC);
			if (empty($checkRes)) {
				$sql2 = "SELECT id, pwd FROM user_delete WHERE email LIKE '$mailOrUsername' OR username LIKE '$mailOrUsername'";
				$check2 = $this->dbHandler->query($sql2);
				$check2->execute();
				$check2Res = $check2->fetchAll(PDO::FETCH_ASSOC);
				if(empty($check2Res)){
					return 0;
				}
				else {
					define("SALT", "vive le projet tweet_academy");
					//Checking if pwd valid
					$pwdHash = $check2Res[0]['pwd'];
					if (hash("ripemd160", $password . SALT) === $pwdHash) {
						$userId = $check2Res[0]['id'];
						return 3 . $userId;
					} else {
						return 2;
					}
				}
			} else {
				define("SALT", "vive le projet tweet_academy");
				//Checking if pwd valid
				$pwdHash = $checkRes[0]['pwd'];
				if (hash("ripemd160", $password . SALT) === $pwdHash) {
					$userId = $checkRes[0]['id'];
					return 1 . $userId;
				} else {
					return 2;
				}
			}
		}

		public function is_connected() {
			session_start();
			if (isset($_SESSION["currentUser"])) {
				return true;
			} else {
				return false;
			}
		}

		// CREATE USER FUNCTION
		public function createUser($username, $firstname, $lastname, $mailAdress, $phoneNumber, $birthdate, $gender, $pwd, $pwdConf) {
			// Checking if mail or username taken
			$sql1 = "SELECT id FROM users WHERE email LIKE '$mailAdress'";
			$check1 = $this->dbHandler->prepare($sql1);
			$check1->execute();
			$checkMail = $check1->fetchAll(PDO::FETCH_ASSOC);
			$sql2 = "SELECT id FROM users WHERE username LIKE '$username'";
			$check2 = $this->dbHandler->prepare($sql2);
			$check2->execute();
			$checkUsername = $check2->fetchAll(PDO::FETCH_ASSOC);
			//If username & mail not taken
			if (empty($checkMail) && empty($checkUsername)) {
				if ($pwd === $pwdConf) {
					define("SALT", "vive le projet tweet_academy");
					$pwdHash = hash("ripemd160", $pwd . SALT);
					$addSql = "INSERT INTO users (username, firstname, lastname, birthdate, gender, pwd, registered_date, email, phone_number) VALUES ('$username', '$firstname', '$lastname', '$birthdate', '$gender', '$pwdHash', CURRENT_TIMESTAMP, '$mailAdress', '$phoneNumber')";
					$addUser = $this->dbHandler->prepare($addSql);
					try {
						$addUser->execute();
						// CREATE A DEFAULT PROFILE
						$qryMaxID = "SELECT MAX(id) as id FROM users";
						$resultQryMaxID = $this->dbHandler->query($qryMaxID);
						$resultQryMaxID->execute();
						$result = $resultQryMaxID->fetch(PDO::FETCH_ASSOC);
						$id = $result['id'];
						$qryInsertProfile = "INSERT INTO profile(id_user, background_url, picture_url, bio, location) VALUES ('$id', 'https://lh3.googleusercontent.com/BnsEMJaboy-i3XOT_AiauRwLz5bsb3eMoNlCABmDMTQT2_gK4YxJLKRS3iUuxVeH0dAvf6R1flLUr85EhJPVoGkMr5MOL-jxkXk=h600', 'https://abs.twimg.com/sticky/default_profile_images/default_profile_400x400.png', 'Describe yourself !', 'Gotham city')";
						$this->dbHandler->exec($qryInsertProfile);
						return 0;
					} catch (PDOException $e) {
						print $e->getMessage();
					}

				} else if ($pwd !== $pwdConf) {
					return 1;
				}
			} else if ($pwd !== $pwdConf) {
				return 1;
			} else {
				if (empty($checkMail) === false && empty($checkUsername)) {
					return 2;
				}
				if (empty($checkMail) && empty($checkUsername) === false) {
					return 3;
				} else if (empty($checkMail) === false && empty($checkUsername) === false) {
					return 4;
				}
			}
		}

		// REACTIVATE USER FUNCTION
		public function reactivate_user($id){
			$sql = "INSERT INTO users SELECT * FROM user_delete WHERE id = '$id'";
			$tmp = $this->dbHandler->prepare($sql);
			$tmp->execute();
			$sql2 = "DELETE FROM user_delete WHERE id = '$id'";
			$tmp2 = $this->dbHandler->query($sql2);
			$tmp2->execute();
			echo "Your account has been reactivated.";
		}

		// DISPLAY USER'S DATAS
		public function user_profile_display($id) {
			$qry = "SELECT users.username, DATE_FORMAT(birthdate,'%Y.%m.%d') as birthdate, DATE_FORMAT(registered_date,'%Y.%m') as registered_date, firstname, lastname, location, background_url, picture_url, bio FROM users LEFT JOIN profile ON profile.id_user = users.id WHERE users.id = '$id'";
			$datas = $this->dbHandler->query($qry);
			$datas->execute();
			$resultProfile = $datas->fetchAll(PDO::FETCH_ASSOC);

			if (empty($resultProfile)) {
				return NULL;
			} else {
				$qry = "SELECT count(id_followed) AS followed FROM user_follow WHERE id_followed = '$id' ";
				$datas = $this->dbHandler->query($qry);
				$datas->execute();
				$resultFollower = $datas->fetchAll(PDO::FETCH_ASSOC);

				$qry = "SELECT count(id_follower) AS follower FROM user_follow WHERE id_follower = '$id' ";
				$datas = $this->dbHandler->query($qry);
				$datas->execute();
				$resultFollowed = $datas->fetchAll(PDO::FETCH_ASSOC);

				$qry = "SELECT COUNT(tweet.id_user) as counts_tweet FROM tweet WHERE id_user = '$id' ";
				$datas = $this->dbHandler->query($qry);
				$datas->execute();
				$resultTweetsCount = $datas->fetchAll(PDO::FETCH_ASSOC);


				$resultProfile[0]['followed'] = $resultFollower[0]['followed'];
				$resultProfile[0]['follower'] = $resultFollowed[0]['follower'];
				$resultProfile[0]['counts_tweet'] = $resultTweetsCount[0]['counts_tweet'];

				return $resultProfile[0];
			}
		}

		// DISPLAY USER'S DATAS INTO EDIT PROFILE
		public function user_profile_edit_display($id) {
			$qry = "SELECT users.username, DATE_FORMAT(birthdate,'%Y.%m.%d') as birthdate, email, location, background_url, picture_url, bio FROM users LEFT JOIN profile ON profile.id_user = users.id WHERE users.id = '$id'";
			// tweet.message, tweet.tweet_date, tweet.url_picture, tweet.likes, tweet.retweet
			$datas = $this->dbHandler->query($qry);
			$datas->execute();
			$result = $datas->fetchAll(PDO::FETCH_ASSOC);

			if (empty($result)) {
				return NULL;
			} else {
				return $result[0];
			}
		}

		// UPDATE PROFILE
		public function user_profile_update($id, $background_picture, $profile_picture, $username, $mail, $bio, $location, $birthdate, $actualPassword, $newPassword) {
			// Checking if mail or username taken
			$mailqry = "SELECT id FROM users WHERE email LIKE '$mail' AND id != '$id'";
			$checkmailqry = $this->dbHandler->prepare($mailqry);
			$checkmailqry->execute();
			$checkMail = $checkmailqry->fetchAll(PDO::FETCH_ASSOC);
			$usernameqry = "SELECT id FROM users WHERE username LIKE '$username' AND id != '$id'";
			$checkusernameqry = $this->dbHandler->prepare($usernameqry);
			$checkusernameqry->execute();
			$checkUsername = $checkusernameqry->fetchAll(PDO::FETCH_ASSOC);
			//If username & mail not taken
			if (empty($checkMail) && empty($checkUsername)) {
				define("SALT", "vive le projet tweet_academy");
				$pwdHash = hash("ripemd160", $actualPassword . SALT);
				$newPwdHash = hash("ripemd160", $newPassword . SALT);
				$qryActualPwd = "SELECT pwd from users where id = '$id'";
				$getPwd = $this->dbHandler->query($qryActualPwd);
				$getPwd->execute();
				$actualPwd = $getPwd->fetchAll(PDO::FETCH_ASSOC);
				if ($pwdHash === $actualPwd[0]['pwd']) {
					echo json_encode("========== THE PWDS MATCHS ! =========");
					echo json_encode($actualPwd[0]['pwd']);
					$qry = "UPDATE users, profile
							SET background_url = '$background_picture', 
			    			picture_url = '$profile_picture', username = '$username', 
			    			birthdate = '$birthdate', pwd = '$newPwdHash', 
			    			email = '$mail', bio = '$bio', location = '$location' WHERE profile.id_user = '$id' AND users.id = '$id'";
					try {
						$datas = $this->dbHandler->query($qry);
						$datas->execute();
						$result = $datas->fetchAll(PDO::FETCH_ASSOC);
						if (empty($result)) {
							return NULL;
						} else {
							return 0;
						}
					} catch (PDOException $e) {
						print $e->getMessage();
					}
				} else if ($pwdHash !== $actualPwd) {
					// THE USER PWD IS DIFFERENT THAT THE ONE ENTERED INTO THE FORM
					return 1;
				}
			} else {
				if (empty($checkMail) === false && empty($checkUsername)) {
					return 2;
				}
				if (empty($checkMail) && empty($checkUsername) === false) {
					return 3;
				} else if (empty($checkMail) === false && empty($checkUsername) === false) {
					return 4;
				}
			}
		}

		// LOG OUT
		public function disconnect() {
			if (session_status() === PHP_SESSION_NONE) {
				session_start();
			}
			session_unset();
		}

		// DISPLAY TWEETS DATAS FROM THE USER CONCERNED
		public function get_tweets_of_specific_user($id) {
			$sql = "SELECT profile.picture_url, users.username, users.firstname, users.lastname, tweet.message, tweet.tweet_date, tweet.url_picture, tweet.likes, tweet.retweet FROM tweet JOIN users ON tweet.id_user = users.id JOIN profile ON users.id = profile.id_user WHERE users.id = '$id' ORDER BY RAND()";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$tweets = $datas->fetchAll(PDO::FETCH_ASSOC);

			if (empty($tweets)) {
				return NULL;
			} else {
				echo json_encode($tweets);
			}
		}

		// GET OTHER USER TWEETS
		public function get_tweets_of_other_user($username) {
			$sql = "SELECT profile.picture_url, users.username, users.firstname, users.lastname, tweet.message, tweet.tweet_date, tweet.url_picture, tweet.likes, tweet.retweet FROM tweet JOIN users ON tweet.id_user = users.id JOIN profile ON users.id = profile.id_user WHERE username = '$username' ORDER BY RAND()";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$tweets = $datas->fetchAll(PDO::FETCH_ASSOC);

			if (empty($tweets)) {
				return NULL;
			} else {
				echo json_encode($tweets);
			}
		}

		// USER'S FOLLOWERS
		public function get_followers_of_specific_user($id) {
			$sql = "SELECT users.username, picture_url FROM users JOIN profile ON users.id = profile.id_user JOIN user_follow ON users.id = user_follow.id_follower WHERE id_followed = '$id' ORDER BY username ASC";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$followers = $datas->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($followers);
		}

		// USER'S FOLLOW
		public function get_follow_of_specific_user($id) {
			$sql = "SELECT users.id, users.username, picture_url FROM users JOIN profile ON users.id = profile.id_user JOIN user_follow ON users.id = user_follow.id_followed WHERE id_follower = '$id' ORDER BY username ASC";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$followed = $datas->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($followed);
		}

		// OTHERS PROFILES
		public function other_profile($username) {
			$qry = "SELECT users.username, DATE_FORMAT(birthdate,'%Y.%m.%d') as birthdate, DATE_FORMAT(registered_date,'%Y.%m') as registered_date, firstname, lastname, location, background_url, picture_url, bio FROM users LEFT JOIN profile ON profile.id_user = users.id WHERE users.username = '$username'";
			$datas = $this->dbHandler->query($qry);
			$datas->execute();
			$resultProfile = $datas->fetchAll(PDO::FETCH_ASSOC);

			if (empty($resultProfile)) {
				return NULL;
			} else {
				$qry = "SELECT count(id_followed) AS followed FROM user_follow JOIN users ON user_follow.id_followed = users.id WHERE username = '$username' ";
				$datas = $this->dbHandler->query($qry);
				$datas->execute();
				$resultFollower = $datas->fetchAll(PDO::FETCH_ASSOC);

				$qry = "SELECT count(id_follower) AS follower FROM user_follow JOIN users ON user_follow.id_follower = users.id WHERE username = '$username' ";
				$datas = $this->dbHandler->query($qry);
				$datas->execute();
				$resultFollowed = $datas->fetchAll(PDO::FETCH_ASSOC);

				$qry = "SELECT COUNT(tweet.id_user) as counts_tweet FROM tweet JOIN users ON tweet.id_user = users.id WHERE username = '$username' ";
				$datas = $this->dbHandler->query($qry);
				$datas->execute();
				$resultTweetsCount = $datas->fetchAll(PDO::FETCH_ASSOC);

				$resultProfile[0]['followed'] = $resultFollower[0]['followed'];
				$resultProfile[0]['follower'] = $resultFollowed[0]['follower'];
				$resultProfile[0]['counts_tweet'] = $resultTweetsCount[0]['counts_tweet'];

				return $resultProfile[0];
			}
		}

		// USER'S FOLLOWERS WITH USERNAME
		public function get_followers_of_other_user($username) {
			$sql = "SELECT users.username, picture_url 
			FROM users JOIN profile ON users.id = profile.id_user 
			JOIN user_follow ON users.id = user_follow.id_follower 
			WHERE id_followed = (
			SELECT users.id FROM users
    		WHERE username = '$username') ORDER BY username ASC;";
    		$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$followers = $datas->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($followers);
		}

		// USER'S FOLLOW WITH USERNAME
		public function get_follow_of_other_user($username) {
			$sql = "SELECT users.username, picture_url 
				FROM users JOIN profile ON users.id = profile.id_user 
			    JOIN user_follow ON users.id = user_follow.id_followed 
				WHERE id_follower = ( 
				    SELECT users.id FROM users 
				    WHERE username = '$username') 
				ORDER BY username ASC;";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$followed = $datas->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($followed);
		}

		// FOLLOW OTHER USER
		public function follow_user($id, $username){
			$sql = "INSERT INTO user_follow (id_follower, id_followed) 
					VALUES ((SELECT users.id FROM users WHERE id = '$id'), 
			        (SELECT users.id FROM users WHERE username = '$username'));";
			$this->dbHandler->exec($sql);
			echo json_encode("follow success");
		}

		// DUPLICATE ACCOUNT INTO USER_DELETE
		public function duplicate_user_account($id) {
			$sql = "INSERT INTO user_delete SELECT * FROM users WHERE users.id = '$id'";
			$tmp = $this->dbHandler->prepare($sql);
			$tmp->execute();
		}

		// UNABLE ACCOUNT
		public
		function unable_user_account($id) {
			// DELETE FROM USERS
			$sql2 = "DELETE FROM users WHERE users.id = '$id'";
			$tmp2 = $this->dbHandler->query($sql2);
			$tmp2->execute();
		}

		// DISPLAY TWEETS DATAS
		public
		function get_tweets() {
			$sql = "SELECT profile.picture_url, users.username, users.firstname, users.lastname, tweet.id, tweet.message, tweet.tweet_date, tweet.url_picture, tweet.likes, tweet.retweet FROM tweet JOIN users ON tweet.id_user = users.id JOIN profile ON users.id = profile.id_user ORDER BY tweet.tweet_date DESC";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$tweets = $datas->fetchAll(PDO::FETCH_ASSOC);

			if (empty($tweets)) {
				return NULL;
			} else {
				echo json_encode($tweets);
			}
		}

		// GET NUMBER OF COMMENTS
		public
		function get_comment_number($tweetId) {
			$sql = "SELECT count(id) as 'nb' FROM comment WHERE id_tweet = '$tweetId'";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$comments = $datas->fetchAll(PDO::FETCH_ASSOC);

			echo json_encode($comments);
		}

		// GET SELECTED TWEET AND COMMENTS
		public
		function get_selected_tweet_comments($tweetId, $userId) {
			$datasArr = [];

			$sql = "SELECT profile.picture_url, users.username, users.firstname, users.lastname, tweet.id, tweet.message, tweet.tweet_date, tweet.url_picture, tweet.likes, tweet.retweet FROM tweet JOIN users ON tweet.id_user = users.id JOIN profile ON users.id = profile.id_user WHERE tweet.id = '$tweetId'";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$tweet = $datas->fetchAll(PDO::FETCH_ASSOC);

			$sqlCom = "SELECT profile.picture_url, users.username, users.firstname, users.lastname, comment.id, comment.message, comment.date_tweet, comment.url_picture, comment.likes, comment.rt FROM comment JOIN users ON comment.id_user = users.id JOIN profile ON users.id = profile.id_user WHERE comment.id_tweet = '$tweetId' ORDER BY comment.date_tweet DESC";
			$datasCom = $this->dbHandler->query($sqlCom);
			$datasCom->execute();
			$comments = $datasCom->fetchAll(PDO::FETCH_ASSOC);

			$sqlPic = "SELECT picture_url FROM profile WHERE id_user = '$userId'";
			$userPic = $this->dbHandler->query($sqlPic);
			$userPic->execute();
			$curUserPic = $userPic->fetchAll(PDO::FETCH_ASSOC);


			$datasArr['tweet'] = $tweet;
			$datasArr['comments'] = $comments;
			$datasArr['currentuser'] = $curUserPic;

			if (empty($datasArr)) {
				return NULL;
			} else {
				echo json_encode($datasArr);
			}
		}

		// GET DATA FOR COMMENTS
		public
		function get_tweet_comment($id, $userId) {
			$datasArr = [];
			$sql = "SELECT profile.picture_url, users.username, users.firstname, users.lastname, tweet.id, tweet.message, tweet.tweet_date, tweet.url_picture FROM tweet JOIN users ON tweet.id_user = users.id JOIN profile ON users.id = profile.id_user WHERE tweet.id = '$id'";
			$datas = $this->dbHandler->query($sql);
			$datas->execute();
			$tweet = $datas->fetchAll(PDO::FETCH_ASSOC);

			$sqlUser = "SELECT picture_url FROM profile WHERE id_user = '$userId'";
			$profile = $this->dbHandler->query($sqlUser);
			$profile->execute();
			$profilePic = $profile->fetch();

			$datasArr['originalTweet'] = $tweet;
			$datasArr['profilePic'] = $profilePic;
			if (empty($datasArr)) {
				return NULL;
			} else {
				echo json_encode($datasArr);
			}
		}

		// RETWEET FUNCTION
		public
		function retweet($id, $userId) {
			$checkRtSql = "SELECT * FROM user_history WHERE id_user = '$userId' AND id_rt_tweet = '$id'";
			$checkRtEx = $this->dbHandler->query($checkRtSql);
			$checkRtEx->execute();
			$checkRt = $checkRtEx->fetchAll(PDO::FETCH_ASSOC);
			if (empty($checkRt)) {
				$addRtSql = "INSERT INTO user_history (id_user, id_rt_tweet, date_tweet) VALUES ('$userId', '$id', CURRENT_TIMESTAMP)";
				$addRt = $this->dbHandler->prepare($addRtSql);
				$addRt->execute();

				$getRtSql = "SELECT retweet FROM tweet WHERE id = '$id'";
				$getRt = $this->dbHandler->query($getRtSql);
				$getRt->execute();
				$nbRt = $getRt->fetchAll(PDO::FETCH_ASSOC);

				$numRt = intval($nbRt[0]['retweet']);
				$numRt++;
				$numRtStr = strval($numRt);

				$pushRtSql = "UPDATE tweet SET retweet = '$numRtStr' WHERE id = '$id'";
				$pushRt = $this->dbHandler->prepare($pushRtSql);
				$pushRt->execute();
			} else {
				$remRtSql = "DELETE FROM user_history WHERE id_user = '$userId' AND id_rt_tweet = '$id'";
				$remRt = $this->dbHandler->prepare($remRtSql);
				$remRt->execute();

				$getRtSql = "SELECT retweet FROM tweet WHERE id = '$id'";
				$getRt = $this->dbHandler->query($getRtSql);
				$getRt->execute();
				$nbRt = $getRt->fetchAll(PDO::FETCH_ASSOC);

				$numRt = intval($nbRt[0]['retweet']);
				$numRt--;
				$numRtStr = strval($numRt);

				$pushRtSql = "UPDATE tweet SET retweet = '$numRtStr' WHERE id = '$id'";
				$pushRt = $this->dbHandler->prepare($pushRtSql);
				$pushRt->execute();
			}
		}


		// RETWEET COMMENT FUNCTION
		public
		function retweet_comment($id, $userId) {
			$checkRtSql = "SELECT * FROM user_history WHERE id_user = '$userId' AND id_rt_comment = '$id'";
			$checkRtEx = $this->dbHandler->query($checkRtSql);
			$checkRtEx->execute();
			$checkRt = $checkRtEx->fetchAll(PDO::FETCH_ASSOC);
			if (empty($checkRt)) {
				$addRtSql = "INSERT INTO user_history (id_user, id_rt_comment, date_tweet) VALUES ('$userId', '$id', CURRENT_TIMESTAMP)";
				$addRt = $this->dbHandler->prepare($addRtSql);
				$addRt->execute();

				$getRtSql = "SELECT rt FROM comment WHERE id = '$id'";
				$getRt = $this->dbHandler->query($getRtSql);
				$getRt->execute();
				$nbRt = $getRt->fetchAll(PDO::FETCH_ASSOC);

				$numRt = intval($nbRt[0]['rt']);
				$numRt++;
				$numRtStr = strval($numRt);

				$pushRtSql = "UPDATE comment SET rt = '$numRtStr' WHERE id = '$id'";
				$pushRt = $this->dbHandler->prepare($pushRtSql);
				$pushRt->execute();
			} else {
				$remRtSql = "DELETE FROM user_history WHERE id_user = '$userId' AND id_rt_comment = '$id'";
				$remRt = $this->dbHandler->prepare($remRtSql);
				$remRt->execute();

				$getRtSql = "SELECT rt FROM comment WHERE id = '$id'";
				$getRt = $this->dbHandler->query($getRtSql);
				$getRt->execute();
				$nbRt = $getRt->fetchAll(PDO::FETCH_ASSOC);

				$numRt = intval($nbRt[0]['rt']);
				$numRt--;
				$numRtStr = strval($numRt);

				$pushRtSql = "UPDATE comment SET rt = '$numRtStr' WHERE id = '$id'";
				$pushRt = $this->dbHandler->prepare($pushRtSql);
				$pushRt->execute();
			}
		}

		// LIKE FUNCTION
		public
		function like($id, $userId) {
			$checkLikeSql = "SELECT * FROM user_history WHERE id_user = '$userId' AND id_liked_tweet = '$id'";
			$checkLikeEx = $this->dbHandler->query($checkLikeSql);
			$checkLikeEx->execute();
			$checkLike = $checkLikeEx->fetchAll(PDO::FETCH_ASSOC);
			if (empty($checkLike)) {
				$addLikeSql = "INSERT INTO user_history (id_user, id_liked_tweet, date_tweet) VALUES ('$userId', '$id', CURRENT_TIMESTAMP)";
				$addLike = $this->dbHandler->prepare($addLikeSql);
				$addLike->execute();

				$getLikeSql = "SELECT likes FROM tweet WHERE id = '$id'";
				$getLike = $this->dbHandler->query($getLikeSql);
				$getLike->execute();
				$nbLike = $getLike->fetchAll(PDO::FETCH_ASSOC);

				$numLike = intval($nbLike[0]['likes']);
				$numLike++;
				$numLikeStr = strval($numLike);

				$pushLikeSql = "UPDATE tweet SET likes = '$numLikeStr' WHERE id = '$id'";
				$pushLike = $this->dbHandler->prepare($pushLikeSql);
				$pushLike->execute();
			} else {
				$remLikeSql = "DELETE FROM user_history WHERE id_user = '$userId' AND id_liked_tweet = '$id'";
				$remLike = $this->dbHandler->prepare($remLikeSql);
				$remLike->execute();

				$getLikeSql = "SELECT likes FROM tweet WHERE id = '$id'";
				$getLike = $this->dbHandler->query($getLikeSql);
				$getLike->execute();
				$nbLike = $getLike->fetchAll(PDO::FETCH_ASSOC);

				$numLike = intval($nbLike[0]['likes']);
				$numLike--;
				$numLikeStr = strval($numLike);

				$pushLikeSql = "UPDATE tweet SET likes = '$numLikeStr' WHERE id = '$id'";
				$pushLike = $this->dbHandler->prepare($pushLikeSql);
				$pushLike->execute();
			}
		}

		// LIKE COMMENT FUNCTION
		public
		function like_comment($id, $userId) {
			$checkLikeSql = "SELECT * FROM user_history WHERE id_user = '$userId' AND id_liked_comment = '$id'";
			$checkLikeEx = $this->dbHandler->query($checkLikeSql);
			$checkLikeEx->execute();
			$checkLike = $checkLikeEx->fetchAll(PDO::FETCH_ASSOC);
			if (empty($checkLike)) {
				$addLikeSql = "INSERT INTO user_history (id_user, id_liked_comment, date_tweet) VALUES ('$userId', '$id', CURRENT_TIMESTAMP)";
				$addLike = $this->dbHandler->prepare($addLikeSql);
				$addLike->execute();

				$getLikeSql = "SELECT likes FROM comment WHERE id = '$id'";
				$getLike = $this->dbHandler->query($getLikeSql);
				$getLike->execute();
				$nbLike = $getLike->fetchAll(PDO::FETCH_ASSOC);

				$numLike = intval($nbLike[0]['likes']);
				$numLike++;
				$numLikeStr = strval($numLike);

				$pushLikeSql = "UPDATE comment SET likes = '$numLikeStr' WHERE id = '$id'";
				$pushLike = $this->dbHandler->prepare($pushLikeSql);
				$pushLike->execute();
			} else {
				$remLikeSql = "DELETE FROM user_history WHERE id_user = '$userId' AND id_liked_comment = '$id'";
				$remLike = $this->dbHandler->prepare($remLikeSql);
				$remLike->execute();

				$getLikeSql = "SELECT likes FROM comment WHERE id = '$id'";
				$getLike = $this->dbHandler->query($getLikeSql);
				$getLike->execute();
				$nbLike = $getLike->fetchAll(PDO::FETCH_ASSOC);

				$numLike = intval($nbLike[0]['likes']);
				$numLike--;
				$numLikeStr = strval($numLike);

				$pushLikeSql = "UPDATE comment SET likes = '$numLikeStr' WHERE id = '$id'";
				$pushLike = $this->dbHandler->prepare($pushLikeSql);
				$pushLike->execute();
			}
		}

		// POST NEW TWEET
		public
		function post_tweet($id, $tweet, $picture) {
			if ($id == "" || $tweet == "") {
				echo null;
			}
			if (empty($picture)) {
				$sql = "INSERT INTO tweet (message, id_user, tweet_date, likes, retweet) VALUES ('$tweet', '$id', CURRENT_TIMESTAMP, '0', '0')";
			} else {
				$sql = "INSERT INTO tweet (message, id_user, url_picture, tweet_date, likes, retweet) VALUES ('$tweet', '$id', '$picture', CURRENT_TIMESTAMP, '0', '0')";
			}
			$tweet = $this->dbHandler->prepare($sql);
			$tweet->execute();
			echo "Tweet Posted!";
		}

		// POST NEW REPLY
		public
		function post_reply($userId, $reply, $reply_pic, $tweetId) {
			$getTweetUserIdSql = "SELECT id_user FROM tweet WHERE id = '$tweetId'";
			$getTweetUserId = $this->dbHandler->query($getTweetUserIdSql);
			$getTweetUserId->execute();
			$tweetUserIdArr = $getTweetUserId->fetchAll(PDO::FETCH_ASSOC);
			$tweetUserId = $tweetUserIdArr[0]['id_user'];

			if (empty($picture)) {
				$sql = "INSERT INTO comment (id_user, message, id_tweet, date_tweet, likes, rt, id_tagged_user) VALUES ('$userId', '$reply', '$tweetId', CURRENT_TIMESTAMP, '0', '0', '$tweetUserId')";
			} else {
				$sql = "INSERT INTO comment (id_user, message, url_picture, id_tweet, date_tweet, likes, rt, id_tagged_user) VALUES ('$userId', '$reply', '$reply_pic', '$tweetId', CURRENT_TIMESTAMP, '0', '0', '$tweetUserId')";
			}
			$reply = $this->dbHandler->prepare($sql);
			$reply->execute();
			echo "Reply Posted!";
		}

		// GET HASTAGS FROM TWEETS
		public
		function getHashtag() {
			// Getting the content of tweets
			$sql = "SELECT message FROM tweet";
			$getTweets = $this->dbHandler->query($sql);
			$hashtagsArr = [];
			$regex = "/[\.\-\_\/]/";
			foreach ($getTweets->fetchAll(PDO::FETCH_ASSOC) as $tweet) {
				$hashtagPos = strpos($tweet['message'], "#");
				if ($hashtagPos !== false) {
					$rawHashtag = strtok(substr($tweet['message'], $hashtagPos), " ");
					//Find multiple #
					$nbrOfHashtag = substr_count($tweet['message'], "#");
					for ($i = 0; $i < $nbrOfHashtag; $i++) {
						// Only if # is followed by smth
						if (strlen($rawHashtag) > 1) {
							if (preg_match($regex, $rawHashtag, $matches)) {
								$hashtagWithoutSpecialChar = substr($rawHashtag, 0, strpos($rawHashtag, $matches[0]));
								array_push($hashtagsArr, $hashtagWithoutSpecialChar);
							} else {
								array_push($hashtagsArr, $rawHashtag);
							}
						}
						$tweetWithoutHashtag = str_replace($rawHashtag, "", $tweet['message']);
						$rawHashtag = strtok(substr($tweetWithoutHashtag, $hashtagPos), " ");
					}
				}
			}
			$filteredHashtagsArr = array_unique($hashtagsArr);
			$countHashtags = [];
			foreach ($filteredHashtagsArr as $hashtag) {
				//SQL query to count how many tweets are linked to this #
				$sql = "SELECT COUNT(id) FROM tweet WHERE message LIKE '%$hashtag%'";
				$query = $this->dbHandler->query($sql);
				foreach ($query->fetchAll(PDO::FETCH_DEFAULT) as $res) {
					array_push($countHashtags, $res[0]);
				}
			}
			$globalArr = array_merge($filteredHashtagsArr, $countHashtags);
			$nbrOfValues = count($globalArr);
			for ($i = 0; $i < $nbrOfValues / 2; $i++) {
				$globalArr[$i] = $globalArr[$i + ($nbrOfValues / 2)] . "." . $globalArr[$i];
			}
			array_splice($globalArr, $nbrOfValues / 2);
			rsort($globalArr, SORT_NUMERIC);
			//Sort array by nbr
			print_r(json_encode($globalArr));
		}

		public function getAllMessages($idUser) {
			$sql = "SELECT *, firstname, lastname, username FROM `direct_messages` JOIN users ON direct_messages.id_receiver = users.id WHERE id_receiver = '$idUser' ORDER BY sent_date DESC ";
			$query = $this->dbHandler->query($sql);
			try {
				$query->execute();
				return $query->fetchAll(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				print $e->getMessage();
			}
		}

		public function getConversation($idSender, $idReceiver) {
			$datas = [];
			$sql1 = "SELECT message, sent_date FROM direct_messages WHERE id_sender = '$idSender' AND id_receiver = '$idReceiver' ORDER BY sent_date ASC";
			$sql2 = "SELECT message, sent_date FROM direct_messages WHERE id_sender = '$idReceiver' AND id_receiver = '$idSender' ORDER BY sent_date ASC";
			$query1 = $this->dbHandler->query($sql1);
			$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
			$query2 = $this->dbHandler->query($sql2);
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$datas['sender'] = $res1;
			$datas['receiver'] = $res2;
			return $datas;
		}

		public
		function getLastMessage($idUser) {
			//Getting all senders
			$sql = "SELECT DISTINCT(id_sender) FROM direct_messages WHERE id_receiver = $idUser";
			$query = $this->dbHandler->query($sql);
			$lastMessageArr = [];
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $sender) {
				$id = $sender['id_sender'];
				$sql = "SELECT * FROM direct_messages JOIN users ON direct_messages.id_sender = users.id  WHERE id_sender = '$id' AND id_receiver = '$idUser' ORDER BY sent_date DESC LIMIT 1";
				$query = $this->dbHandler->query($sql);
				array_push($lastMessageArr, $query->fetchAll(PDO::FETCH_ASSOC));
			}
			return $lastMessageArr;
		}

	public function writeMessage($idUser, $idReceiver, $message) {
		$sql = "INSERT INTO direct_messages (id_sender, id_receiver, message, sent_date) VALUES ('$idUser', '$idReceiver', '$message', CURRENT_TIMESTAMP)";
		try {
			$this->dbHandler->query($sql);
			echo "sent";
		}
		catch(PDOException $e) {
			print $e->getMessage();
		}
	}
}