<!DOCTYPE html>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'php/functions.php'
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>samply.scheduler</title>
    <link rel="icon" href="favicon.png" type="image/png" sizes="32x32">
    <link rel="stylesheet" href="css/fontawesome-all.min.css">
    <link href="css/filepond.css" rel="stylesheet">
    <link rel="stylesheet" href="css/flatpickr.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h1>Samply Scheduler</h1>
    <p>Free to use.</p>
    <?php
        if (isLoggedIn()) {
            include 'php/includes/Schedule.php';
        } else {
            include 'php/includes/SignIn.php';
        }
    ?>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Get FilePond polyfills from the CDN -->
<script src="js/filepond-polyfill.js"></script>
<!-- Get FilePond JavaScript from the CDN -->
<script src="js/filepond.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="js/prettydate.js"></script>
<script src="js/js.cookie.min.js"></script>
<script src="js/scheduler.js"></script>

</body>
</html>