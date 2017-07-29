/* 
 * Embedding code for various platforms
 * IMPORTANT:
 * This file isn't loaded directly, for changes to show you must generate a minified
 * version. E.g.
 *
 *   yui-compressor embeds.js > embeds.min.js
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

function Unfurl() {}

Unfurl.fetch = function (url, object_id, callback) {

    $.getJSON(known.config.displayUrl + 'service/web/unfurl/',
	    {
		object_id: object_id,
		url: url
	    },
	    function (data) {
		callback(data);
		
	    }
    );
}
