L.mapbox.accessToken = 'pk.eyJ1IjoibGJyYWNjbyIsImEiOiJjanRveTY3NjkwNDRsM3lwYjJzbHZ6NGt0In0.nG3DLfKEJvpaIK69TFagPQ';
var celular = L.mapbox.map('map').setView([ -27.39878,-55.9439991], 2).addLayer(L.mapbox.styleLayer('mapbox://styles/mapbox/streets-v11'));

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
        mqtt.subscribe(topic, {qos: 2});
        
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
        
        var split = payload.split(":");
        console.log(topic)
        console.log(split[0]+"-"+split[1]);
    if (res[0]=="CELULAR")
    {
      if (res[2]=="GPS")
      {
        var featureLayer = L.mapbox.featureLayer().addTo(celular);
        featureLayer.setGeoJSON({
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [split[1], split[0]]
            },
            properties: {
                'title': split[2]+","+split[3]+"<br>Celular:"+split[4]+"<br>Direccion:"+split[5],
                'marker-color': '#ff8888',
                'marker-symbol': 'star'
            }
        });
        console.log(message.payloadString);    
        
        
        
      }
    }
     
    
      if (res[0]=="CENTRAL")
    {
        console.log(message.destinationName);    
        console.log(message.payloadString);    
     
      if (res[1]=="PANICO" || res[1]=="SALUD" || res[1]=="ALERTA" || res[1]=="VDG")
      {
        var icono;
        switch (res[1]) {
            case "SALUD":
                    icono="hospital"
              break;
            case "PANICO":
              icono="police";
              break;
            case "ALERTA":
              icono = "danger";
              break;
            default:
                icono = "danger";
              break;
          }

          Lobibox.notify('warning', {
              sound: false,
              title:res[1]+" - Cel:"+split[2],
              delay: false,
              msg: 
                '<br>Nombre:'+split[0]+
                '<br>Apellido:'+split[1]
                
            });
      }
    } 
}


    $(document).ready(function() {
        MQTTconnect();
        
    });

    
$("#BtnGrabar").click(function(){
    $.ajax({
        type: 'GET',
        url: '?event=grabar',
        data: {	
            id : $("#id").val(),
            nombre : $("#nombre").val(),
            apellido : $("#apellido").val(),
            celular : $("#celular").val(),
            direccion : $("#direccion").val(),
            estado : $("#estado").val()
              },
        dataType: "html",
        success: function(msg){

          Editar(msg);  
        }
        });
       
});
