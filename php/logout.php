<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/12/2018
 * Time: 3:03 PM
 */

setcookie("screen_name", "", time() - 3600, "/");
setcookie("oauth_token", "", time() - 3600, "/");
setcookie("oauth_token_secret", "", time() - 3600, "/");
session_start();
session_destroy();
session_unset();

header('Location: http://scheduler.samply.co/');