<?php
require "config.php";
require "db.php";

//For connection
if ($_POST['signIn']) {
    $check = new Database();
    $res =  $check->signIn($_POST['mailOrUsername'], $_POST['password']);
    session_start();
    $_SESSION["currentUser"] = substr($res, 1);
    print $res . SID;
}

//For signing up
else {
    $add = new Database();
    $res = $add->createUser($_POST['username'], $_POST['firstname'], $_POST['lastname'], $_POST['mail'], $_POST['phoneNumber'], $_POST['birthdate'], $_POST['gender'], $_POST['password'], $_POST['passwordConfirmation']);
    print $res;
}
