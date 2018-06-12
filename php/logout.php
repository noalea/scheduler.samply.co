<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/12/2018
 * Time: 3:03 PM
 */

session_start();
session_destroy();
session_unset();

header('Location: http://codeyourfreedom.com/scheduler/');