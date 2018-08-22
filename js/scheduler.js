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

    function nth(d) {
        if(d>3 && d<21) return 'th'; // thanks kennebec
        switch (d % 10) {
            case 1:  return "st";
            case 2:  return "nd";
            case 3:  return "rd";
            default: return "th";
        }
    }

    function dateToWords(date) {
        var year, month, day, prettyDate, monthArr = [];

        monthArr = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        year = date.split("-")[0];
        month = monthArr[parseInt(date.split("-")[1]) - 1];
        day = parseInt(date.split("-")[2]);
        prettyDate = month + " "  + day + nth(day) + ", " + year;
        return prettyDate;
    }

    function organizeSchedule(d) {
        $(".scheduled-tweets").empty();
        var sections = [];
        console.log(d);
        if (d[2] != null) {
            var prev = d[2][0];
            var day = "<h1>" + dateToWords(d[2][0]) + "</h1>";
            sections.push(day);
            for (var i = 0; i < d[2].length; i++) {
                var curr = d[2][i];
                if (prev != curr) {
                    prev = curr;
                    day = "<h1>" + dateToWords(d[2][i]) + "</h1>";
                    sections.push(day);
                }
                var scheduledFor = moment(d[2][i] + ' ' + d[3][i]).format('MM/DD/YYYY h:mma');
                var collage = CollageMaker();
                var info = "<div class='tweet-preview' id='tweet-"+ d[8][i] +"'>" +
                                "<div class='delete-tweet'><span></span><span></span></div>" +
                                "<div class='scheduled-for'>" + scheduledFor.split(' ')[1] + "</div>" +
                                "<div class='left'><img src='"+ d[7][i] +"' /></div>" +
                                "<div class='right'>" +
                                    "<div class='top'>" +
                                        "<p><b>"+ d[6][i] +"</b></p>" +
                                        "<p>@"+ d[5][i] +"</p>" +
                                    "</div>" +
                                    "<div class='content'>" +
                                        "<p>"+ d[0][i] +"</p>" +
                                    "</div>" +
                                    "<div class='bottom'>" +
                                        "<img src='images/respond.png' />" +
                                        "<img src='images/retweet.png' />" +
                                        "<img src='images/like.png' />" +
                                        "<img src='images/stats.png' />" +
                                    "</div>" +
                                "</div>" +
                           "</div>";
                sections.push(info);
            }
            for (var j = 0; j < sections.length; j++) {
                $(sections[j]).appendTo(".scheduled-tweets");
            }
        } else {
            $("<p class='empty-scheduled'>You have no scheduled tweets yet! You should start...</p>").appendTo(".scheduled-tweets");
        }

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
            getScheduledTweets();
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

    function deleteTweet(e) {
        var tweetId, tsid;
        tweetId = e.currentTarget.offsetParent.id;
        tsid = tweetId.split("-")[1];

        var data = JSON.stringify({
           tsid: tsid
        });
        $.ajax({
            type: 'POST',
            url: './php/deleteTweet.php',
            data: data
        })
        .done(function(data) {
            console.log(data);
            getScheduledTweets();
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
        $(document).on("click", ".delete-tweet", deleteTweet);
    }

    function setup() {
        $(".prettydate").prettydate();
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