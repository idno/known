'use strict';

// detect native/existing fragmention support
if (!('fragmention' in window.location)) (function () {
    // populate fragmention
    location.fragmention = location.fragmention || '';

    // return first element in scope containing case-sensitive text
    function getElementByText(scope, text) {
        // iterate descendants of scope
        for (var all = scope.childNodes, index = 0, element; (element = all[index]); ++index) {
            // conditionally return element containing visible, whitespace-insensitive, case-sensitive text (a match)
            if (element.nodeType == 1 && (element.innerText || element.textContent || '').replace(/\s+/g, ' ').indexOf(text) !== -1) {
                return getElementByText(element, text);
            }
        }

        // return scope (no match)
        return scope;
    }

    // on dom ready or hash change
    function onHashChange() {
        // set location fragmention as uri-decoded text (from href, as hash may be decoded)
        location.fragmention = decodeURIComponent((location.href.match(/#(#|%23)(.+)/) || [0,0,''])[2].replace(/\+/g, ' '));

        // conditionally remove stashed element fragmention attribute
        if (element) {
            element.removeAttribute('fragmention');

            // DEPRECATED: trigger style in IE8
            if (element.runtimeStyle) {
                element.runtimeStyle.windows = element.runtimeStyle.windows;
            }
        }

        // if fragmention exists
        if (location.fragmention) {
            // get element containing text (or return document)
            element = getElementByText(document, location.fragmention);

            // if element found
            if (element !== document) {
                // scroll to element
                element.scrollIntoView();

                // set fragmention attribute
                element.setAttribute('fragmention', '');

                // DEPRECATED: trigger style in IE8
                if (element.runtimeStyle) {
                    element.runtimeStyle.windows = element.runtimeStyle.windows;
                }
            }
            // otherwise clear stashed element
            else {
                element = null;
            }
        }
    }

    // set stashed element
    var element;

    // add listeners
    if ('addEventListener' in window) {
        window.addEventListener('hashchange', onHashChange);
        document.addEventListener('DOMContentLoaded', onHashChange);
    }
    // DEPRECATED: otherwise use old IE attachEvent
    else {
        window.attachEvent('onhashchange', onHashChange);
        document.attachEvent('onreadystatechange', function () {
            if (document.readyState[0] === 'c') {
                onHashChange();
            }
        });
    }
})();
