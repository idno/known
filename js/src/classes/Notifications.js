
/** Known notifications */
var Notifications = Notifications || {};

/**
 * Poll for new notifications
 */
Notifications.poll = function () {
    $.get(known.config.displayUrl + 'service/notifications/new-notifications')
	    .done(function (data) {
		//console.log("Polling for new notifications succeeded");
		//console.log(data);
		if (data.notifications)
		    if (data.notifications.length > 0) {
			for (var i = 0; i < data.notifications.length; i++) {
			    var title = data.notifications[i].title;
			    var body = data.notifications[i].body;
			    var icon = data.notifications[i].icon;
			    var link = data.notifications[i].link;
			    try {
				var notification = new Notification(title, {
				    icon: icon,
				    body: body,
				    data: link
				});
                                /*jshint ignore:start*/
				notification.onclick = function(e) {
				    window.location.href = link;
				};
                                /*jshint ignore:end*/
			    } catch (e) {
				// We have to use service worker, as New doesn't work
				
				// TODO : Implement
			    }
			}
		    }
	    })
	    .fail(function (data) {
		//console.log("Polling for new notifications failed");
	    });
};

Notifications.enable = function (opt_dontAsk) {
    if (!known.session.loggedIn) {
	return;
    }

    if (!("Notification" in window)) {
	console.log("The Notification API is not supported by this browser");
	return;
    }
    
    // New method click handling
    self.addEventListener('notificationclick', function(event) {
	window.location.href = event.notification.data;
    });
    
    if (Notification.permission !== 'denied' && Notification.permission !== 'granted' && !opt_dontAsk) {
	Notification.requestPermission(function (permission) {
	    // If the user accepts, let's create a notification
	    if (permission === "granted") {
		setInterval(Notifications.poll, 30000);
	    }
	});
    } else if (Notification.permission === 'granted') {
	setInterval(Notifications.poll, 30000);
    }
};

/**
 * Have notifications been granted?
 * @returns {Boolean}
 */
Notifications.isEnabled = function () {
    if (!known.session.loggedIn) {
	return false;
    }

    if (!("Notification" in window)) {
	console.log("The Notification API is not supported by this browser");
	return false;
    }
    if (Notification.permission === 'granted') {
	return true;
    }

    return false;
};


// Backwards compatibility for those who already have notifications installed
function doPoll() {
    Notifications.poll();
}

// If we've granted permission (via the notifications page), then lets configure a poll for new notifications
$(document).ready(function () {
    if (Notifications.isEnabled()) {
	Notifications.enable(true); // Don't pester asking for permission, only do that on notifications page
    }
});
