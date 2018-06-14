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
            time: time
        });

        $.ajax({
            type: 'POST',
            url: './php/scheduleTweet.php',
            data: data
        })
        .done(function(data) {
            var d = JSON.parse(data);
            console.log('Data:', d);
        })
        .fail(function(err) {
            console.log(err);
        });
    }

    function setupEventListeners() {
        $(".tweetnow button").click(tweetNow);
        $(".tweetlater button").click(tweetLater);
    }

    function setup() {
        $(".datetime-picker").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            altInput: true,
            altFormat: "F j, Y H:i",
            minDate: "today"
        });
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