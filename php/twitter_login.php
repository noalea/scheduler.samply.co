<?php

require_once '../twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

$config = require_once 'config.php';

// create TwitterOAuth object
$connection = new TwitterOAuth($config['consumer_key'], $config['consumer_secret']);

// request token of application
$request_token = $connection->oauth(
    'oauth/request_token', [
        'oauth_callback' => $config['url_callback']
    ]
);

// throw exception if something gone wrong
if($connection->getLastHttpCode() != 200) {
    throw new \Exception('There was a problem performing this request');
}

// save token of application to session
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];


// generate the URL to make request to authorize our application
$url = $connection->url(
    'oauth/authorize', [
        'oauth_token' => $request_token['oauth_token']
    ]
);

// and redirect
header('Location: '. $url);