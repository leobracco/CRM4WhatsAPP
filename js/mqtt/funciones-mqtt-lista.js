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
        
       
        Lobibox.notify('success', {
            sound: false,
            size: 'mini',
            msg: 'Se conecto al server.'
        });
        // Connection succeeded; subscribe to our topic
        mqtt.subscribe(topic, {qos: 0});
    }

    function onConnectionLost(response) {
        Lobibox.notify('error', {
            sound: false,
            size: 'mini',
            msg: 'Se desconecto del server.'
        });
        setTimeout(MQTTconnect, reconnectTimeout);
        

    };

    function onMessageArrived(message) {
        
        var topic = message.destinationName;
        var payload = message.payloadString;
        var res = topic.split("/");
        
	
	if (res[2]=="Temperatura")
	    $("#"+res[1]).html(payload+"&deg;C");
	if (res[2]=="Humedad")
	    $("#"+res[1]).html(payload+"%");
    if (res[2]=="TENSION"||res[2]=="BATERIA")
    {
        $("#"+res[1]).html(payload+"&nbsp;&nbsp;Volts");
       
    }
	if (res[2]=="SIGNAL")
	    $("#"+res[1]+"-signal").html(payload+"&nbsp;&nbsp;RSSI");
	if (res[2]=="IMEI")
	    $("#"+res[1]+"-imei").html(payload);
// 	 num.toFixed(2);
    };


    $(document).ready(function() {
        MQTTconnect();
    });

function Off(nserie){
        console.log("Nserie:"+nserie);
        mqtt.send(nserie+"/OFF","OK");
}
function LuzOn(nserie){
    console.log("Nserie:"+nserie);
    mqtt.send(nserie+"/LUZON","OK");
}
function LuzOff(nserie){
    console.log("Nserie:"+nserie);
    mqtt.send(nserie+"/LUZOFF","OK");
}
function Check(nserie){
    console.log("Nserie:"+nserie);
    mqtt.send(nserie+"/CHECK","OK");
}

function Imei(nserie){
    console.log("IMEI:"+nserie);
    mqtt.send(nserie+"/IMEI","OK");
}
function Signal(nserie){
    console.log("Nserie:"+nserie);
    mqtt.send(nserie+"/SIGNAL","OK");
}
function Bateria(nserie){
    console.log("Bateria Nserie:"+nserie);
    mqtt.send(nserie+"/BATERIA", "OK");
}
