
function contentCreateForm(plugin, editUrl) {
    Template.initContentCreateForm(plugin, editUrl);
}

function hideContentCreateForm() {
    Template.hideContentCreateForm();
}


/**
 * Periodically send the current values of this form to the server.
 *
 * @deprecated use Template.autoSave()
 */
function autoSave(context, elements, selectors) {
    return Template.autoSave(context, elements, selectors);
}

/** Configure timeago and adjust videos in content */
function annotateContent() {
    $(".h-entry").fitVids({ customSelector: "iframe[src^='https://www.bitchute.com'], iframe[src^='https://archive.org']"});
    $("time.dt-published").timeago();
}

/**
 * Better handle links in iOS web applications.
 * This code (from the discussion here: https://gist.github.com/kylebarrow/1042026)
 * will prevent internal links being opened up in safari when known is installed
 * on an ios home screen.
 */
(function (document, navigator, standalone) {
    if ((standalone in navigator) && navigator[standalone]) {
        var curnode, location = document.location, stop = /^(a|html)$/i;
        document.addEventListener('click', function (e) {
            curnode = e.target;
            while (!(stop).test(curnode.nodeName)) {
                curnode = curnode.parentNode;
            }
            if ('href' in curnode && (curnode.href.indexOf('http') || ~curnode.href.indexOf(location.host)) && (!curnode.classList.contains('contentTypeButton'))) {
                e.preventDefault();
                location.href = curnode.href;
            }
        }, false);
    }
})(document, window.navigator, 'standalone');

// Open external links in new tab
$(document).ready(function(){
    $('body').on('click', function (event, el) {
        var clickTarget = event.target;

        if (clickTarget.href && clickTarget.href.indexOf(window.location.origin) === -1) {
            clickTarget.target = "_blank";
        }
    });
});

/**
 * Initialise some template features.
 */
$(document).ready(function(){
    $.timeago.settings.cutoff = 30 * 24 * 60 * 60 * 1000; // 1 month
    annotateContent();
    
    Template.enableFormCandy();
    Template.enablePagination();
    Template.enableRichTextRequired();
    Template.enableImageFallback();
    Template.activateStarToggle();
    Template.enableTooltips();
    Template.enableDateTimePicker();
});
