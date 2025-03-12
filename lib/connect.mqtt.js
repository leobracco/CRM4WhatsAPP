var mqtt;
var reconnectTimeout = 2000;
var host; // hostname or IP address
var port;
var topic; // topic to subscribe to
var topicP;
var useTLS;
var username;
var password;
var cleansession;
var path;
console.log("HOST:" + host + " PASS:" + password)

function MQTTconnect() {
    if (typeof path == "undefined") {
        path = '/';
    }
    mqtt = new Paho.MQTT.Client(
        host,
        port,
        "QII-CLOUD-" + parseInt(Math.random() * 10000, 10)

    );
    var options = {
        timeout: 3,
        useSSL: useTLS,
        cleanSession: cleansession,
        onSuccess: onConnect,
        onFailure: function(message) {
            console.log("Connection failed: " + message.errorMessage + "Retrying");
            setTimeout(MQTTconnect, reconnectTimeout);
        }
    };
    mqtt.onConnectionLost = onConnectionLost;
    mqtt.onMessageArrived = onMessageArrived;
    if (username != null) {
        options.userName = username;
        options.password = password;
    }
    console.log("Host=" + host + ", port=" + port + ", path=" + path + " TLS = " + useTLS + " username=" + username + " password=" + password);
    mqtt.connect(options);
}

function onConnect() {
    $('#status').val('Connected to ' + host + ':' + port + path);
    // Connection succeeded; subscribe to our topic
    mqtt.subscribe(topic, { qos: 0 });

    //topicMessage = new Paho.MQTT.Message(JSON.stringify({ action: "WHOIS" }));
    topicMessage = new Paho.MQTT.Message("test");
    topicMessage.topic = topicMaster;
    mqtt.publish(topicMessage)
    topicMessage.topic = topicSlave;
    mqtt.publish(topicMessage)

}



function onConnectionLost(response) {
    setTimeout(MQTTconnect, reconnectTimeout);
    console.log("connection lost: " + response.errorMessage + ". Reconnecting");
};



(function() {
    var url = "../credenciales/?event=config";
    $.getJSON(url, { format: "json" })
        .done(function(data) {
            host = data.host; // hostname or IP address
            port = data.port;
            topic = data.topic; // topic to subscribe to
            topicMaster = data.topicMaster; // topic to subscribe to
            topicSlave = data.topicSlave; // topic to subscribe to
            useTLS = data.secure;
            username = data.username;
            password = data.token;
            cleansession = data.session;
            path = data.path;
            MQTTconnect();
        });
})();