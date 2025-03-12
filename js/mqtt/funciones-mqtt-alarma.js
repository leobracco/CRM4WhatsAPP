var mqtt;
    var reconnectTimeoutAlarma = 2000;

    function MQTTconnectAlarma() {
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
            onSuccess: onConnectAlarma,
            onFailure: function (message) {
                setTimeout(MQTTconnectAlarma, reconnectTimeoutAlarma);
            }
        };

        mqtt.onConnectionLost = onConnectionLostAlarma;
        mqtt.onMessageArrived = onMessageArrivedAlarma;

        if (username != null) {
            options.userName = username;
            options.password = password;
        }
        
        mqtt.connect(options);
    }

    function onConnectAlarma() {
        console.log('Connected to ' + host + ':' + port + path+$("#nserie").val());
        mqtt.subscribe("ALARMA/"+$("#nserie").val()+"/#", {qos: 0});
    }

    function onConnectionLostAlarma(response) {
        setTimeout(MQTTconnectAlarma, reconnectTimeoutAlarma);
    };

    function onMessageArrivedAlarma(message) {

        var topic = message.destinationName;
        var payload = message.payloadString;
	
	var res = topic.split("/");
 	//console.log(res[0]+"-"+res[1]+"-"+res[2]);
	$("#log").html($("#log").html()+"\n"+payload);
    }
    $(document).ready(function() {
        MQTTconnectAlarma();
    });

