<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {

    //URLパラメーターで渡ってきた値でpostsテーブルから内容を取得
    $rt_posts = $db->prepare('SELECT * FROM posts WHERE id=?');
    $rt_posts->execute(array($_REQUEST['retweet_post_id']));
    $rt_post = $rt_posts->fetch();

    //取得した内容を再度リツイートされた投稿として内容を加え、投稿する
    $re_tweet = $db->prepare('INSERT INTO posts SET message=?, member_id=?, retweet_post_id=?, retweet_member_id=?, created=NOW(), retweet_flag=1');
    $re_tweet->execute(array(
        $rt_post['message'],
        $rt_post['member_id'],
        $_REQUEST['retweet_post_id'],
        $_REQUEST['retweet_member_id']
    ));



    header('Location: index.php');
    exit();
}
