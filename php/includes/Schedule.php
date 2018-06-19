<?php require_once 'php/functions.php' ?>

<?php $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "php/submit.php"; ?>

<div class="user-bar">
    <span>Signed in as </span>
    <p><?php echo getName(); ?></p>
    <img class="prof-pic" src="<?php echo getProfilePicture(); ?>"/>
    <a class="logout" href="php/logout.php">Log Out</a>
</div>

<form action="<?php echo $url; ?>" class="tweetnow" method="post" enctype="multipart/form-data">
    <h2>Tweet Now</h2>
    <input type="text" name="status" placeholder="What's happening?">
    <input type="file" name="filepond[]" multiple>
    <button type="submit" class="twitter_bg" name="submit">Tweet</button>
</form>

<form action="<?php echo $url; ?>" class="tweetlater" method="post" enctype="multipart/form-data">
    <h2>Schedule A Tweet</h2>
    <input type="text" name="status" placeholder="What's happening?">
    <input type="file" name="filepond[]" multiple>
    <input type="text" name="date" class="datetime-picker" placeholder="Pick a time.">
    <button type="submit" class="twitter_bg" name="submit">Tweet</button>
</form>


