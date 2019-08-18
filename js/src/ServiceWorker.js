/**
 * Stub Known service worker.
 * 
 * This file is deployed by a virtual page so that it appears at the top, and is easily overridable 
 * by applications which want to provide a more useful service worker thread.
 * 
 * IMPORTANT:  This file isn't loaded directly, for changes to show you must generate a minified
 * version by executing the Gruntfile. See: http://docs.withknown.com/en/latest/developers/build/
 */

"use strict";

/* Babel ES6 runtime polyfils */
import "core-js/stable";
import "regenerator-runtime/runtime";

function KnownServiceWorker() {}