<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])){

    $like_increment = $db->prepare('INSERT INTO likes SET like_member_id=?, like_post_id=?');
    $like_increment->execute(array(
        $_REQUEST['like_member_id'],
        $_REQUEST['like_post_id']
    ));
}
header('Location: index.php');
exit();