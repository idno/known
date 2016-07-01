/**
 Helper Javascript functions for Known

 If you need to add your own JavaScript, the best thing to do is to create your own js files
 and reference them from a custom plugin or template.

 @package idno
 @subpackage core
 */

/**
 * Globals
 */

var isCreateFormVisible = false;

/**
 *** Content creation
 */

function bindControls() {
    $('.acl-option').click(function () {
        $('#access-control-id').val($(this).attr('data-acl'));
        $('#acl-text').html($(this).html());
    });
    $('.syndication-toggle input[type=checkbox]').bootstrapToggle();
    $('.ignore-this').hide();
}

function contentCreateForm(plugin, editUrl) {
    if (isCreateFormVisible) {
        // Ignore additional clicks on create button
        return;
    }

    isCreateFormVisible = true;
    $.ajax(editUrl, {
        dataType: 'html',
        success: function (data) {
            $('#contentCreate').html(data).slideDown(400);
            $('#contentTypeButtonBar').slideUp(400);
            window.contentCreateType = plugin;
            window.contentPage = true;

            bindControls();
        },
        error: function (error) {
            $('#contentTypeButtonBar').slideDown(400);
            isCreateFormVisible = false;
        }

    });
}

function hideContentCreateForm() {
    isCreateFormVisible = false;
    if (window.contentPage == true) {
        $('#contentTypeButtonBar').slideDown(200);
        $('#contentCreate').slideUp(200);
    } else {
        //window.close(); // Will only fire for child windows
        if (window.history.length > 1) {
            window.history.back();
        }
    }
}

/**
 * Periodically send the current values of this form to the server.
 *
 * @param string context Usually the type of entity being saved. We keep one autosave
 *     for each unique context.
 * @param array elements The elements to save, e.g. ["title", "body"].
 * @param object selectors (optional) A mapping from element name to its unique
 *     JQuery-style selector. If no mapping is provided, defaults to "#element";
 */
function autoSave(context, elements, selectors) {
    var previousVal = {};
    setInterval(function () {
        var changed = {};
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            var selector = "#" + element;
            if (selectors && element in selectors) {
                selector = selectors[element];
            }
            var val = false;
            if ($(selector).val() != previousVal[element]) {
                val = $(selector).val();
            }
            if (val !== false) {
                changed[element] = val;
                previousVal[element] = val;
            }
        }
        if (Object.keys(changed).length > 0) {
            $.post(wwwroot() + 'autosave/',
                {
                    "context": context,
                    "elements": changed,
                    "names": elements
                },
                function () {
                }
            );
        }
    }, 10000);
}

/**
 * Strip HTML from string
 * @param html
 * @returns {string}
 */
function knownStripHTML(html) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
}

/**
 * Are we in an iFrame?
 * @returns {boolean}
 */
function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

/**
 * Decode HTML elements
 * @param encodedString
 * @returns {string}
 */
function htmlEntityDecode(encodedString) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = encodedString;
    return textArea.value;
}

/*
 * Shim so that JS functions can get the current site URL
 * @deprecated Use known.config.displayUrl
 */
function wwwroot() {
    return known.config.displayUrl;
}

/**
 * Shim so JS functions can tell if this is a logged in session or not.
 * @deprecated Use known.session.loggedin
 * @returns {Boolean}
 */
function isLoggedIn() {
    if (typeof known !== 'undefined')
        if (known.session.loggedIn) {
            return true;
        }
    return false;
}

/**
 * Poll for new notifications
 */
function doPoll() {
    $.get(known.config.displayUrl + '/account/new-notifications')
        .done(function (data) {
            console.log("Polling for new notifications succeeded");
            console.log(data);
            if (data.notifications)
            if (data.notifications.length > 0) {
                var title = data.notifications[0].title;
                var body = data.notifications[0].body;
                new Notification(title, {body: body});
            }
        })
        .fail(function (data) {
            console.log("Polling for new notifications failed");
        });
}

function enableNotifications(opt_dontAsk) {
    if (!known.session.loggedIn) {
        return;
    }

    if (!("Notification" in window)) {
        console.log("The Notification API is not supported by this browser");
        return;
    }

    if (Notification.permission !== 'denied' && Notification.permission !== 'granted' && !opt_dontAsk) {
        Notification.requestPermission(function (permission) {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
                setInterval(doPoll, 10000);
            }
        });
    } else if (Notification.permission === 'granted') {
        setInterval(doPoll, 10000);
    }
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
        //enableNotifications(true);
    }
});