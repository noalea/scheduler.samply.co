/*  -----------------
FILEPOND SETTINGS
-----------------   */
FilePond.parse(document.body);

// Set default server location
FilePond.setOptions({
    server: './php/'
});
// Create ponds on the page
var pond = FilePond.create( document.querySelector('input[type="file"]') );

var scheduler = (function () {

    function tweet(e) {
        e.preventDefault();
        var status, data, images = [];
        status = $(".tweetnow input[name='status']").val();

        $('.filepond--file-wrapper legend').each(function(i, obj) {
            var fileTitle = $(obj).html(), fileURL;
            fileTitle = encodeURIComponent(fileTitle.trim());
            fileURL = "http://codeyourfreedom.com/scheduler/php/tmp/1/" + fileTitle;
            images.push(fileURL);
        });

        console.log(images);

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

    function setupEventListeners() {
        $(".tweetnow button").click(tweet);
    }

    return {
        init: function () {
            console.log('App has started.');
            setupEventListeners();
        }
    };
})();

scheduler.init();