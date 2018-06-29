<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/14/2018
 * Time: 2:51 PM
 */

session_start();

$db = require_once 'db/conn.php';

$user = $_COOKIE['screen_name'];

$select = "SELECT S.*, U.t_username, U.name, U.picture
           FROM TweetSchedule S, Users U
           WHERE U.t_username='$user' AND U.uid=S.uid
           ORDER BY S.on_day, S.on_time";
$result = mysqli_query($db, $select);

while ($row = mysqli_fetch_assoc($result)) {
    $status[] = $row['status'];
    $media[] = $row['media'];
    $on_day[] = $row['on_day'];
    $on_time[] = $row['on_time'];
    $timezone[] = $row['timezone'];
    $t_username[] = $row['t_username'];
    $name[] = $row['name'];
    $picture[] = $row['picture'];
}

$arr = array($status, $media, $on_day, $on_time, $timezone, $t_username, $name, $picture);


echo json_encode($arr);
