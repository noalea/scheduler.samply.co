<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/11/2018
 * Time: 12:02 PM
 */
header('Content-Type: text/html; charset=utf-8');
require_once '../twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

$db = require_once 'db/conn.php';
$config = require_once 'config.php';

$oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');

if (empty($oauth_verifier) ||
    empty($_SESSION['oauth_token']) ||
    empty($_SESSION['oauth_token_secret'])
) {
    // something's missing, go and login again
    header('Location: ' . $config['url_login']);
}

// connect with application token
$connection = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $_SESSION['oauth_token'],
    $_SESSION['oauth_token_secret']
);

// request user token
$token = $connection->oauth(
    'oauth/access_token', [
        'oauth_verifier' => $oauth_verifier
    ]
);

//$_SESSION['token'] = $token;


$oauth_token = $token['oauth_token'];
$oauth_token_secret = $token['oauth_token_secret'];

$user_connection = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $oauth_token,
    $oauth_token_secret
);
$user = $user_connection->get("account/verify_credentials");

$selectUID = "SELECT `uid` FROM Users WHERE t_username='".$user->screen_name."'";
$get = mysqli_query($db, $selectUID);
$row = mysqli_fetch_row($get);
$uid = $row[0];

// check
$select = "SELECT * FROM Users WHERE uid='".$uid."'";
$check = mysqli_query($db, $select);

if (mysqli_num_rows($check) > 0){
    // user exists : update db
    $update = "UPDATE Users 
               SET t_username='$user->screen_name', name='$user->name', picture='$user->profile_image_url'
               WHERE oauth_token='".$oauth_token."'";
    mysqli_query($db, $update);
} else {
    // user is new : insert info into db
    $insert = "INSERT INTO Users (t_username, name, picture)
               VALUES ('$user->screen_name', '$user->name', '$user->profile_image_url')";
    mysqli_query($db, $insert);

    $selectUID = "SELECT `uid` FROM Users WHERE t_username='".$user->screen_name."'";
    $get = mysqli_query($db, $selectUID);
    $row = mysqli_fetch_row($get);
    $uid = $row[0];

    // user is new : insert tokens into db
    $insertTokens = "INSERT INTO TwitterTokens (uid, oauth_token, oauth_token_secret)
               VALUES ('$uid', '$oauth_token', '$oauth_token_secret')";
    mysqli_query($db, $insertTokens);
}

setcookie('screen_name', $user->screen_name, time() + (86400 * 30), "/");
setcookie('oauth_token', $token['oauth_token'], time() + (86400 * 30), "/");
setcookie('oauth_token_secret', $token['oauth_token_secret'], time() + (86400 * 30), "/");

// and redirect
header('Location: http://codeyourfreedom.com/scheduler/');