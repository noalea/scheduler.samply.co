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

$_SESSION['token'] = $token;

$oauth_token = $token['oauth_token'];
$oauth_token_secret = $token['oauth_token_secret'];

$user_connection = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $oauth_token,
    $oauth_token_secret
);
$user = $user_connection->get("account/verify_credentials");

// check
$select = "SELECT * FROM users WHERE oauth_token='".$oauth_token."'";
$check = mysqli_query($db, $select);

if (mysqli_num_rows($check) > 0){
    // user exists : update db
    $update = "UPDATE users 
               SET username='$user->screen_name', name='$user->name', picture='$user->profile_background_image_url_https'
               WHERE oauth_token='".$oauth_token."'";
    mysqli_query($db, $update);
} else {
    // user is new : insert into db
    $insert = "INSERT INTO users (oauth_token, oauth_token_secret, username, name, picture)
               VALUES ('$oauth_token', '$oauth_token_secret', '$user->screen_name', '$user->name', '$user->profile_background_image_url_https')";
    mysqli_query($db, $insert);
}

$_SESSION['screen_name'] = $user->screen_name;

// and redirect
header('Location: http://codeyourfreedom.com/scheduler/');