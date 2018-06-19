<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/12/2018
 * Time: 2:45 PM
 */

$db = require_once 'db/conn.php';

session_start();

function isLoggedIn() {
    return isset($_COOKIE['screen_name']) ? true : false;
}

function getScreenName() {
    return $_COOKIE['screen_name'];
}

function getName() {
    global $db;
    $username = $_COOKIE['screen_name'];
    $select = "SELECT `name` 
               FROM Users
               WHERE t_username='$username'";
    $result = mysqli_query($db, $select);
    $row = mysqli_fetch_assoc($result);
    return $row['name'];
}

function getProfilePicture() {
    global $db;
    $username = $_COOKIE['screen_name'];
    $select = "SELECT `picture` 
               FROM Users
               WHERE t_username='$username'";
    $result = mysqli_query($db, $select);
    $row = mysqli_fetch_assoc($result);
    return $row['picture'];
}