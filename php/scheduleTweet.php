<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/14/2018
 * Time: 2:51 PM
 */

$data = file_get_contents("php://input");
$receivedData = json_decode($data);

$status = $receivedData->status;
$images = $receivedData->images;
$day = $receivedData->day;
$time = $receivedData->time;

session_start();

$db = require_once 'db/conn.php';
$config = require_once 'config.php';

$token = $_SESSION['token'];

$consumer_key = $config['consumer_key'];
$consumer_secret = $config['consumer_secret'];
$oauth_token = $token['oauth_token'];
$oauth_token_secret = $token['oauth_token_secret'];

$user = $_COOKIE['screen_name'];

$select = "SELECT `uid` FROM Users WHERE t_username='".$user."'";
$get = mysqli_query($db, $select);
$row = mysqli_fetch_row($get);
$uid = $row[0];

$media = "";
foreach ($images as $key => $img) {
    if ($media == "") {
        $media = $media . $img;
    } else {
        $media = $media . "," . $img;
    }
}

// add schedule info
$insert = "INSERT INTO TweetSchedule (uid, consumer_key, consumer_secret, oauth_token, oauth_token_secret, status, media, on_day, on_time)
                  VALUES ('$uid', '$consumer_key', '$consumer_secret', '$oauth_token', '$oauth_token_secret', '$status', '$media', '$day', '$time')";
mysqli_query($db, $insert);



//
//$media_ids = array();
//foreach ($media as $m) {
//    array_push($media_ids, $m->media_id_string);
//}
//
//$parameters = [
//    'status' => $status,
//    'media_ids' => implode(',', $media_ids)
//];
//
//$status = $twitter->post('statuses/update', $parameters);
//

//


echo json_encode('Scheduled');
