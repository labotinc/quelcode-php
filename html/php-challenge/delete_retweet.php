<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    $delete = $db->prepare('DELETE FROM posts WHERE retweet_member_id=? AND retweet_post_id=?');
    $delete->execute(array(
        $_REQUEST['retweet_member_id'],
        $_REQUEST['retweet_post_id']
    ));
}

header('Location: index.php');
exit();
