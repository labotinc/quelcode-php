<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {

    $delete_likes = $db->prepare('DELETE FROM likes WHERE like_member_id=? AND like_post_id=?');
    $delete_likes->execute(array(
        $_REQUEST['like_member_id'],
        $_REQUEST['like_post_id']
    ));
}

header('Location: index.php');
exit();
