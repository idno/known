<?php

    header('Content-type: text/javascript');

?>

importScripts('/external/jquery/jquery.min.js');

var apiPort;
var ports = [];
onconnect = function(e) {
    var port = e.ports[0];
    ports.push(port);
    port.onmessage = function (msgEvent)
    {
        var msg = msgEvent.data;
        if (msg.topic == "social.port-closing") {
            if (port == apiPort) {
                apiPort.close();
                apiPort = null;
            }
            return;
        }
        if (msg.topic == "social.initialize") {
            apiPort = port;
            initializeAmbientNotifications();
            getProfile();
        }
    }
}

// send a message to all provider content
function broadcast(topic, data) {
    for (var i = 0; i < ports.length; i++) {
        ports[i].postMessage({topic: topic, data: data});
    }
}

function getProfile() {
}