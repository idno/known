

/*
 * Shim so that JS functions can get the current site URL
 * @deprecated Use known.config.displayUrl
 */
function wwwroot() {
    //Logger.deprecated("wwwroot() is deprecated, use known.config.displayUrl");
    return known.config.displayUrl;
}

/**
 * Shim so JS functions can tell if this is a logged in session or not.
 * @deprecated Use known.session.loggedin
 * @returns {Boolean}
 */
function isLoggedIn() {
    //Logger.deprecated("isLoggedIn() is deprecated, use known.session.loggedin");
    if (typeof known !== 'undefined')
	if (known.session.loggedIn) {
	    return true;
	}
    return false;
}

/**
 * Actions to perform on page load 
 */
$(document).ready(function () {
    var url = $('#soft-forward').attr('href');

    if (!!url) {
	window.location = url;
    }
    
    if (known.session.loggedIn) {
	//TODO(ben) re-enable in a smarter way
	//Notifications.enable(true);
    }
});
