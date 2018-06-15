<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/11/2018
 * Time: 12:33 PM
 */

$data = file_get_contents("php://input");
$receivedData = json_decode($data);

$status = $receivedData->status;
$images = $receivedData->images;

require_once '../twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

$db = require_once 'db/conn.php';
$config = require_once 'config.php';

$oauth_token = $_COOKIE['oauth_token'];
$oauth_token_secret = $_COOKIE['oauth_token_secret'];

$twitter = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $oauth_token,
    $oauth_token_secret
);

$media = array();
foreach ($images as $key => $img) {
    $url_parts = parse_url($img);
    $filepath = $_SERVER['DOCUMENT_ROOT'].$url_parts['path'];
    array_push($media, $twitter->upload('media/upload', ['media' => $filepath]));
}

$media_ids = array();
foreach ($media as $m) {
    array_push($media_ids, $m->media_id_string);
}

$parameters = [
    'status' => $status,
    'media_ids' => implode(',', $media_ids)
];

$status = $twitter->post('statuses/update', $parameters);

$user = $_COOKIE['screen_name'];

$select = "SELECT `uid` FROM Users WHERE t_username='".$user."'";
$get = mysqli_query($db, $select);
$row = mysqli_fetch_row($get);
$uid = $row[0];

// add status->id & screen_name to db
$insert = "INSERT INTO Tweets (uid, tweet_id)
                  VALUES ('$uid', '$status->id')";
mysqli_query($db, $insert);

echo json_encode($status->id);
