/* 
 * Embedding code for various platforms
 * IMPORTANT:
 * This file isn't loaded directly, for changes to show you must generate a minified
 * version. E.g.
 *
 *   yui-compressor embeds.js > embeds.min.js
 */

"use strict";

function Unfurl() {}

/**
 * Attempt to unfurl a url, extracting, title, open graph and oembed information
 * @param {type} url URL to unfurl
 * @param {type} callback Callback which will receive the success return
 */
Unfurl.fetch = function (url, callback) {

    if (url.length > 0) {
	Security.getCSRFToken(function(token, ts) {
	    $.getJSON(known.config.displayUrl + 'service/web/unfurl/',
		    {
			url: url,
			__bTk: token,
			__bTs: ts
		    },
		    function (data) {
			callback(data);

		    }
	    );
	}, known.config.displayUrl + 'service/web/unfurl/');
    }
}

/**
 * Extract all urls in the text.
 * @param {type} text
 * @returns array
 */
Unfurl.getUrls = function (text) {
    console.log(text);
    var urlRegex = new RegExp('(https?:\/\/[^\\s]+)', "gi");

    return text.match(urlRegex);
}

/**
 * Find the first url in the text.
 * @param {type} text
 * @returns {Unfurl.getFirstUrl.urls}
 */
Unfurl.getFirstUrl = function (text) {

    var urls = Unfurl.getUrls(text);
console.log(urls);
    if ((urls != undefined) && (urls.length > 0))
	return urls[0];
    
    return '';
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
	var format = oembed.attr('data-format');

	if (dataurl != undefined) {

	    console.log("Fetching oembed code from " + dataurl + " using " + format);
	    $.ajax({
		url: dataurl,
		dataType: format,
		success: function (data) {
			console.log("Got a response back");
			
			if (format == 'xml') {
			    
			    console.log("XML Format");
			    
			    var $xml = $(data);
			    var txt = $xml.find("html").text();
			    
			    if (txt.indexOf('CDATA') > -1) {
				txt = txt.substr(9, txt.length-12);
			    }
			
			    oembed.html(txt);
			} else {
			    console.log("JSON Format");
			    
			    oembed.html(data['html']);
			}
			
			oembed.closest('.unfurled-url').find('.basics').hide(); // Hide basics, since we have an oembed
		    }
		}
	    );
	}
    }
}

/**
 * 
 * @param {type} control
 * @returns {undefined}Enable edit controls on unfurl links.
 */
Unfurl.enableControls = function (control) {
    
    var url = control.attr('data-url');
    var unfurlblock = control.closest('.unfurl-block');
    var refresh = unfurlblock.find('.unfurl-edit a.refresh');
    var remove = unfurlblock.find('.unfurl-edit a.delete');
    
    refresh.click(function(e){
	
	Security.getCSRFToken(function(token, ts) {
	    $.ajax(known.config.displayUrl + 'service/web/unfurl/', {
		dataType: 'json',
		method: 'GET',
		data: {
		    url: url,
		    forcenew: true,
		    __bTk: token,
		    __bTs: ts
		},
		success: function (data) {
		    console.log("Refreshed");
		    control.fadeOut().fadeIn();
		    Unfurl.unfurl(control);
		}
	    });
	}, known.config.displayUrl + 'service/web/unfurl/');
	
	e.preventDefault();
    });
    
    
    
    remove.click(function(e){
	
	Security.getCSRFToken(function(token, ts) {
	    $.ajax(known.config.displayUrl + 'service/web/unfurl/remove/'  + unfurlblock.attr('data-parent-object') + '/', {
		dataType: 'json',
		method: 'POST',
		data: {
		    __bTk: token,
		    __bTs: ts
		},
		success: function (data) {
		    console.log("Refresh: deleted");
		    unfurlblock.fadeOut();
		}
	    });
	}, known.config.displayUrl + 'service/web/unfurl/remove/' + unfurlblock.attr('data-parent-object') + '/');
	
	e.preventDefault();
    });
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
	   Template.enableImageFallback(); // Reactivate image fallback for broken images
	   Unfurl.enableControls(control);
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