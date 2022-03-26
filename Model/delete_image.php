<?php

$srcfile = $_POST['path'];

if(!unlink($srcfile)){
    echo 'invalid';
}
else {
    echo 'file deleted';
}

?>