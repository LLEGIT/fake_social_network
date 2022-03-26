<?php
require_once "./config.php";
require_once "./db.php";

$tweet = new Database();
$res = $tweet->getHashtag();