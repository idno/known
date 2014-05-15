dump("yo");

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
            //initializeAmbientNotifications();
            getProfile();
            dump("Howdy");
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
    var xhr = new XMLHttpRequest();
    xhr.open("GET", '/currentUser?_t=json', false);
    xhr.send();
    dump("dump");
    dump(xhr.responseText);
    data = JSON.parse(xhr.responseText);
    user = data['user'];
    var userData = {
        portrait: user['image']['url'],
        userName: user['displayName'],
        displayName: user['displayName'],
        profileURL: user['url']
    };
    apiPort.postMessage({topic: "social.user-profile", data: userData});
}