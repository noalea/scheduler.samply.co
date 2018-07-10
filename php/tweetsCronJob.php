<?php
/**
 * Created by PhpStorm.
 * User: Noa Lea
 * Date: 6/14/2018
 * Time: 3:34 PM
 */

require_once '../twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

$db = require_once 'db/conn.php';
$config = require_once 'config.php';



$selectTimezones = "SELECT timezone
                    FROM TweetSchedule";
$resultTimezones = mysqli_query($db, $selectTimezones);

while ($loopTimezones = mysqli_fetch_assoc($resultTimezones)) {
    date_default_timezone_set($loopTimezones['timezone']);
    $day = date("Y-m-d");
    $time = date("H:i:s");

    // Find scheduled tweets ($day = TweetSchedule.on_day and $time >= TweetSchedule.on_time)
    $select = "SELECT *
           FROM TweetSchedule
           WHERE on_day<='$day' AND on_time<='$time'";
    $result = mysqli_query($db, $select);

    while ($row = mysqli_fetch_assoc($result)) {

        $tsid[] = $row['tsid'];
        $uid[] = $row['uid'];
        $status[] = $row['status'];
        $medias[] = $row['media'];
        $ontime[] = $row['on_time'];

        $currUID = $row['uid'];
        $currTSID = $row['tsid'];

        // Find tokens of current user
        $selectTokens = "SELECT oauth_token, oauth_token_secret
                     FROM TwitterTokens
                     WHERE uid='$currUID'";
        $resultTokens = mysqli_query($db, $selectTokens);
        $rowTokens = mysqli_fetch_assoc($resultTokens);

        $twitter = new TwitterOAuth(
            $config['consumer_key'],
            $config['consumer_secret'],
            $rowTokens['oauth_token'],
            $rowTokens['oauth_token_secret']
        );

        $mediaArr = explode(',', $row['media']);

        $media = array();
        foreach ($mediaArr as $key => $img) {
            $url_parts = parse_url($img);
            $filepath = $_SERVER['DOCUMENT_ROOT'].$url_parts['path'];
            array_push($media, $twitter->upload('media/upload', ['media' => $filepath]));
        }

        $media_ids = array();
        foreach ($media as $m) {
            array_push($media_ids, $m->media_id_string);
        }

        $parameters = [
            'status' => $row['status'],
            'media_ids' => implode(',', $media_ids)
        ];

        echo "Parameters:";
        print_r($parameters);

        $tweet = $twitter->post('statuses/update', $parameters);

        // add status->id & screen_name to db
        $insert = "INSERT INTO Tweets (uid, tweet_id)
                  VALUES ('$currUID', '$tweet->id')";
        mysqli_query($db, $insert);

        $delete = "DELETE FROM TweetSchedule
               WHERE tsid='$currTSID'";
        mysqli_query($db, $delete);
    }

}

$data = array($tsid, $uid, $status, $medias, $ontime, $day, $time);

echo "Data: ";
print_r($data);
