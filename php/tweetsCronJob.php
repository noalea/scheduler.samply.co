<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/14/2018
 * Time: 3:34 PM
 */

$db = require_once 'db/conn.php';

$day = date("Y-m-d");
$time = date("h:i:s");

// Find scheduled tweets ($day = TweetSchedule.on_day and $time >= TweetSchedule.on_time)
$select = "SELECT *
           FROM TweetSchedule
           WHERE on_day<='$day' AND on_time<='$time'";

$result = mysqli_query($db, $select);
while ($row = mysqli_fetch_assoc($result)) {
    $tsid[] = $row['tsid'];
    $uid[] = $row['uid'];
    $consumer_key[] = $row['consumer_key'];
    $consumer_secret[] = $row['consumer_secret'];
    $oauth_token[] = $row['oauth_token'];
    $oauth_token_secret[] = $row['oauth_token_secret'];
    $status[] = $row['status'];
    $media[] = $row['media'];
}

$data = array($tsid, $uid, $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $status, $media);

print_r($data);
