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

/**
 * Attempt to unfurl a url, extracting, title, open graph and oembed information
 * @param {type} url URL to unfurl
 * @param {type} callback Callback which will receive the success return
 */
Unfurl.fetch = function (url, callback) {

    $.getJSON(known.config.displayUrl + 'service/web/unfurl/',
	    {
		url: url
	    },
	    function (data) {
		callback(data);

	    }
    );
}

/**
 * Extract all urls in the text.
 * @param {type} text
 * @returns array
 */
Unfurl.getUrls = function (text) {
    var urlRegex = new RegExp("(https?:\/\/[^\s]+)", "g");

    return text.match(urlRegex);
}

/**
 * Find the first url in the text.
 * @param {type} text
 * @returns {Unfurl.getFirstUrl.urls}
 */
Unfurl.getFirstUrl = function (text) {

    var urls = Unfurl.getUrls(text);

    if (urls.length > 0)
	return urls[0];
}

/**
 * Initialise any oembeds found in a specific control.
 * @param {type} control
 * @returns {undefined}
 */
Unfurl.initOembed = function (control) {
    var oembed = control.find('div.oembed');
    if (oembed != undefined) {
	var dataurl = oembed.attr('data-url');

	if (dataurl != undefined) {

	    console.log("Fetching oembed code from " + dataurl);
	    $.getJSON(dataurl,
		    {
			
		    },
		    function (data) {
			oembed.html(data['html']);
		    }
	    );
	}
    }
}

/**
 * Unfurl a specific embedded control
 * @param {type} control
 * @returns {undefined}
 */
Unfurl.unfurl = function (control) {
    var url = control.attr('data-url');
    
    if (url != undefined) {
	Unfurl.fetch(url, function(data) {
	   control.html(data.rendered);
	   control.show();
	   Unfurl.initOembed(control);
	});
    }
}


Unfurl.unfurlAll = function () {
    $('div.unfurl').each(function () {
	Unfurl.unfurl($(this));
    });
}

$(document).ready(function () {
    Unfurl.unfurlAll();
});