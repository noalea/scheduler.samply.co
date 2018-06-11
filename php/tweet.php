<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/11/2018
 * Time: 12:33 PM
 */

require_once '../twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

$config = require_once 'config.php';

$token = $_SESSION['token'];

$twitter = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $token['oauth_token'],
    $token['oauth_token_secret']
);

$status = $twitter->post(
    "statuses/update", [
        "status" => "..."
    ]
);

echo ('Created new status with #' . $status->id . PHP_EOL);