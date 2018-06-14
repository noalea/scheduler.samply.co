<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/12/2018
 * Time: 2:45 PM
 */

session_start();

function isLoggedIn() {
    return isset($_COOKIE['screen_name']) ? true : false;
}

