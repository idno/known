
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
