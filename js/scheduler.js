var scheduler = (function () {

    function tweet(e) {
        e.preventDefault();
        var status, data = {};
        status = $(".tweetnow input[name='status']").val();

        data = JSON.stringify({
           status: status
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