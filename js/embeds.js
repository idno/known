/* 
 * Embedding code for various platforms
 */



/**
 * Handle Twitter tweet embedding
 */
$(document).ready(function () {
    $('div.twitter-embed').each(function (index) {
        var url = $(this).attr('data-url');
        var div = $(this);

        $.ajax({
            url: "https://api.twitter.com/1/statuses/oembed.json?url=" + url,
            dataType: "jsonp",
            success: function (data) {
                div.html(data['html']);
            }
        });
    });

    $('body').on('click', function (event, el) {
        var clickTarget = event.target;

        if (clickTarget.href && clickTarget.href.indexOf(window.location.origin) === -1) {
            clickTarget.target = "_blank";
        }
    });
});

/**
 * Handle Soundcloud oEmbed code
 */
$(document).ready(function () {
    $('div.soundcloud-embed').each(function (index) {
        var url = $(this).attr('data-url');
        var div = $(this);

        $.getJSON('https://soundcloud.com/oembed?callback=?',
                {
                    format: 'js',
                    url: url,
                    iframe: true
                },
                function (data) {
                    div.html(data['html']);
                }
        );
    });
});