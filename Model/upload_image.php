<?php

$_FILES['file'];

$img = $_FILES['file']['name'];
$tmp = $_FILES['file']['tmp_name'];

$path = "Assets/tweet-images/";
$image = rand(1000,1000000).$img;

$path = $path.strtolower($image);

if(move_uploaded_file($tmp,$path)){
    echo $path;
}
else {
    echo 'invalid';
}

?>