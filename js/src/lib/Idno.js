

/*
 * Shim so that JS functions can get the current site URL
 * @deprecated Use idno.config.displayUrl
 */
function wwwroot() {
    //Logger.deprecated("wwwroot() is deprecated, use idno.config.displayUrl");
    return idno.config.displayUrl;
}

/**
 * Shim so JS functions can tell if this is a logged in session or not.
 * @deprecated Use idno.session.loggedin
 * @returns {Boolean}
 */
function isLoggedIn() {
    //Logger.deprecated("isLoggedIn() is deprecated, use idno.session.loggedin");
    if (typeof idno !== 'undefined')
	if (idno.session.loggedIn) {
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
});
