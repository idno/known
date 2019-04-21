
/** Known Javascript logging */
var Logger = Logger || {};

Logger.log = function (message, level) {

    if (typeof level === 'undefined')
	level = 'INFO';

    switch (level.toUpperCase()) {
	case "ALERT":
	case "ERROR":
	case "EXCEPTION":
	    level = "ERROR";
	    console.error(level + ": " + message);
	    break;
	
	case "WARN":
	case "WARNING": 
	    level = "WARNING";
	    console.warn(level + ": " + message);
	    break;
	    
	default: 
	    level = "INFO";
	    console.log(level + ": " + message);
    }

    Security.getCSRFToken(function (token, ts) {
	$.ajax({
	    type: "POST",
	    data: {
		    level: level,
		    message: message,
		    __bTk: token,
		    __bTs: ts
		},
	    url: known.config.displayUrl + 'service/system/log/',
	});
    }, known.config.displayUrl + 'service/system/log/');

};

Logger.info = function(message) {
    Logger.log(message, 'INFO');
};

Logger.warn = function(message) {
    Logger.log(message, 'WARN');
};

Logger.error = function(message) {
    Logger.log(message, 'ERROR');
};

Logger.deprecated = function(message) {
    Logger.info('DEPRECATED ' + message);
};

Logger.errorHandler = function (error) {

    var stack = error.error.stack;
    var message = error.error.toString();

    if (stack) {
	message += '\n' + stack;
    }

    console.error(error);
    Logger.log(message, 'ERROR');
};


/** Default error/exception handler */
window.addEventListener('error', function (e) { Logger.errorHandler(e); });
