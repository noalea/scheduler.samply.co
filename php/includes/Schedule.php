<?php $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "php/submit.php"; ?>

<form action="<?php echo $url; ?>" class="tweetnow" method="post" enctype="multipart/form-data">
    <input type="text" name="status" placeholder="What's happening?">
    <input type="file" name="filepond[]" multiple>
    <button type="submit" class="twitter_bg" name="submit">Tweet</button>
</form>


<a href="php/logout.php">Log Out</a>