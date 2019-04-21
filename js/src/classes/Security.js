
/** Known security object */
var Security = Security || {};

/** Cached tokens */
Security.tokens = [];

/** Perform a HEAD request on the current page and pass the token to a given callback */
Security.getCSRFToken = function (callback, pageurl) {

    if (pageurl == undefined)
	pageurl = known.currentPageUrl;
    
    var time = Math.floor(Date.now() / 1000);
    
    for (var i = 0; i < Security.tokens.length; i ++) {
	if ((Security.tokens[i].url == pageurl) && (Security.tokens[i].time > time - 100)) {
	    console.log('Returning cached token for ' + pageurl);
	    
	    callback(Security.tokens[i].token, Security.tokens[i].time);
	}
    }

    $.ajax({
	type: "GET",
	data: {url: pageurl},
	url: known.config.displayUrl + 'service/security/csrftoken/'
    }).done(function (message, text, jqXHR) {

	Security.tokens.push({
	    token: message.token,
	    time: message.time,
	    url: pageurl
	});

	callback(message.token, message.time);
    });
};

/** Refresh all security tokens */
Security.refreshTokens = function () {

    $('.known-security-token').each(function () {
	var form = $(this).closest('form');

	Security.getCSRFToken(function (token, ts) {

	    form.find('input[name=__bTk]').val(token);
	    form.find('input[name=__bTs]').val(ts);

	}, form.find('input[name=__bTa]').val());
    });
};

setInterval(function () {
    Security.refreshTokens();
}, 300000);

/** 
 * Initialise ACL controls
 */
Security.activateACLControls = function () {
    $('.acl-ctrl-option').each(function () {
	if ($(this).data('acl') == $(this).closest('.access-control-block').find('input').val()) {
	    $(this).closest('.btn-group').find('.dropdown-toggle').html($(this).html() + ' <span class="caret"></span>');
	}
    });
    $('.acl-ctrl-option').on('click', function () {
	$(this).closest('.access-control-block').find('input').val($(this).data('acl'));
	$(this).closest('.btn-group').find('.dropdown-toggle').html($(this).html() + ' <span class="caret"></span>');
	$(this).closest('.btn-group').find('.dropdown-toggle').click();
    });
};

$(document).ready(function () {
    Security.activateACLControls();
});

