<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/29/2018
 * Time: 4:22 PM
 */

$data = file_get_contents("php://input");
$receivedData = json_decode($data);

$tsid = $receivedData->tsid;

session_start();

$db = require_once 'db/conn.php';

$delete = "DELETE FROM TweetSchedule
           WHERE tsid='$tsid'";
mysqli_query($db, $delete);

echo json_encode('Deleted: ' . $tsid);