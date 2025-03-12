var mqtt;
    var reconnectTimeout = 2000;

    function MQTTconnect() {
	if (typeof path == "undefined") {
		path = '/mqtt';
	}
	mqtt = new Paho.MQTT.Client(
			host,
			port,
			path,
			"web_" + parseInt(Math.random() * 100, 10)
	);
        var options = {
            timeout: 3,
            useSSL: useTLS,
            cleanSession: cleansession,
            onSuccess: onConnect,
            onFailure: function (message) {
                setTimeout(MQTTconnect, reconnectTimeout);
            }
        };

        mqtt.onConnectionLost = onConnectionLost;
        mqtt.onMessageArrived = onMessageArrived;

        if (username != null) {
            options.userName = username;
            options.password = password;
        }
        
        mqtt.connect(options);
    }

    function onConnect() {
        $('#status').val('Connected to ' + host + ':' + port + path);
        mqtt.subscribe("ALARMA/#", {qos: 0});
    }

    function onConnectionLost(response) {
        setTimeout(MQTTconnect, reconnectTimeout);
    };

    function onMessageArrived(message) {

        var topic = message.destinationName;
        var payload = message.payloadString;
	
	var res = topic.split("/");
// 	console.log(res[0]+"-"+res[1]+"-"+res[2]);
	if (res[2]=="TENSION"){
	    $("#"+res[1]).html(payload+" Volts");
	
	}
	if (res[2]=="IMEI"){
	    $("#"+res[1]+"-imei").html(payload);
	
	}
	if (res[2]=="SIGNAL"){
	    $("#"+res[1]+"-signal").html(payload+" RSSI");
	
	}
    }
    $(document).ready(function() {
        MQTTconnect();
    });

