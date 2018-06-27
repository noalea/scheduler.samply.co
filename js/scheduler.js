/*  -----------------
FILEPOND SETTINGS
-----------------   */
FilePond.parse(document.body);

// Set default server location
FilePond.setOptions({
    server: './php/'
});
// Create ponds on the page
var pond = FilePond.create( document.querySelector('.tweetnow input[type="file"]') );
var pond2 = FilePond.create( document.querySelector('.tweetlater input[type="file"]') );

var scheduler = (function () {

    function organizeSchedule(d) {
        var sections = [];
        var prev = d[2][0];
        var day = "<h1>" + d[2][0] + "</h1>";
        sections.push(day);
        for (var i = 0; i < d[2].length; i++) {
            var curr = d[2][i];
            if (prev != curr) {
                prev = curr;
                day = "<h1>" + d[2][i] + "</h1>";
                sections.push(day);
            }
            var info = "<p>" + d[0][i] + "</p>";
            sections.push(info);
        }
        for (var j = 0; j < sections.length; j++) {
            $(sections[j]).appendTo(".scheduled-tweets");
        }
        console.log(sections);
    }

    function tweetNow(e) {
        e.preventDefault();
        var status, data, images = [];
        status = $(".tweetnow input[name='status']").val();

        $('.tweetnow .filepond--file-wrapper legend').each(function(i, obj) {
            var fileTitle = $(obj).html(), fileURL;
            fileTitle = encodeURIComponent(fileTitle.trim());
            fileURL = "http://codeyourfreedom.com/scheduler/php/tmp/1/" + fileTitle;
            images.push(fileURL);
        });

        data = JSON.stringify({
            status: status,
            images: images
        });
        
        $.ajax({
            type: 'POST',
            url: './php/tweet.php',
            data: data
        })
        .done(function(data) {
            var d = JSON.parse(data);
            console.log('Data:', d);
            reset("now");
            $(".tweetnow button").html("Tweeted Successfully!");
            $(".tweetnow button").addClass("success");
            setTimeout(function () {
                $(".tweetnow button").html("Tweet");
                $(".tweetnow button").removeClass("success");
            }, 3000);
        })
        .fail(function(err) {
            console.log(err);
        });
    }

    function tweetLater(e) {
        e.preventDefault();
        var status, data, images = [], date, day, time;
        status = $(".tweetlater input[name='status']").val();

        $('.tweetlater .filepond--file-wrapper legend').each(function(i, obj) {
            var fileTitle = $(obj).html(), fileURL;
            fileTitle = encodeURIComponent(fileTitle.trim());
            fileURL = "http://codeyourfreedom.com/scheduler/php/tmp/1/" + fileTitle;
            images.push(fileURL);
        });

        date = $(".tweetlater input[name='date']").val();
        day = date.split(" ")[0];
        time = date.split(" ")[1];

        data = JSON.stringify({
            status: status,
            images: images,
            day: day,
            time: time,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        });

        $.ajax({
            type: 'POST',
            url: './php/scheduleTweet.php',
            data: data
        })
        .done(function(data) {
            var d = JSON.parse(data);
            console.log('Data:', d);
            reset("later");
            $(".tweetlater button").html("Tweet Scheduled!");
            $(".tweetlater button").addClass("success");
            setTimeout(function () {
                $(".tweetlater button").html("Tweet");
                $(".tweetlater button").removeClass("success");
            }, 3000);
        })
        .fail(function(err) {
            console.log(err);
        });
    }
    
    function getScheduledTweets() {
        $.ajax({
            type: 'GET',
            url: './php/getScheduledTweets.php'
        })
        .done(function(data) {
            var d = JSON.parse(data);
            organizeSchedule(d);
        })
        .fail(function(err) {
            console.log(err);
        });
    }

    function open(e) {
        var id, name;
        id = e.currentTarget.id;
        name = id.split("-")[0];
        $("." + name + "-container").addClass("open");
    }

    function close(e) {
        var target;
        target = e.target.classList[0];
        if (target === "tweetnow-container" || target === "tweetlater-container") {
            $("." + target).addClass("close");
            setTimeout(function () {
                $("." + target).removeClass("open close");
            }, 2000);
        }
    }
    
    function reset(f) {
        switch (f) {
            case "now":
                $('form.tweetnow').trigger("reset");
                pond.restoreElement();
                pond = FilePond.create( document.querySelector('.tweetnow input[type="file"]') );
                break;
            case "later":
                $('form.tweetlater').trigger("reset");
                pond2.restoreElement();
                pond2 = FilePond.create( document.querySelector('.tweetlater input[type="file"]') );
                break;
        }
    }

    function setupEventListeners() {
        $(".tweetnow button").click(tweetNow);
        $(".tweetlater button").click(tweetLater);
        $(".tweetnow-container, .tweetlater-container").click(close);
        $(".tweet-buttons p").click(open);
    }

    function setup() {
        $(".datetime-picker").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            altInput: true,
            altFormat: "F j, Y H:i",
            minDate: "today"
        });
        if ($(".scheduled-tweets")[0] !== undefined) {
            getScheduledTweets();
        }
    }

    return {
        init: function () {
            console.log('App has started.');
            setupEventListeners();
            setup();
        }
    };
})();

scheduler.init();