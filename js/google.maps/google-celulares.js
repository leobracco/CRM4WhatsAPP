var latitud = null;
var lng = null;
var map = null;
var geocoder = null;
var marker = null;
var markersCelulares=[];
var celular;
var counterCelulares=0;
jQuery(document).ready(function(){initialize();});
function initialize() {
    
    geocoder = new google.maps.Geocoder();
    
   
	var latLng = new google.maps.LatLng(-38.9620889,-68.0365626);
   
    //Definimos algunas opciones del mapa a crear
    var myOptions = {
	center: latLng,//centro del mapa
	zoom: 8,//zoom del mapa
	scrollwheel: true,
	mapTypeId: google.maps.MapTypeId.ROADMAP //tipo de mapa, carretera, híbrido,etc
    };
    //creamos el mapa con las opciones anteriores y le pasamos el elemento div
    celular = new google.maps.Map(document.getElementById("map"), myOptions);
	
   
   
    
	
}

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
        mqtt.subscribe(topic, {qos: 1});
        console.log("Me suscribo a :"+topic);
        
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
        
    if (res[0]=="CELULAR")
    {
      if (res[2]=="GPS")
      {
        console.log(message.payloadString);      
        counterCelulares++;
        var latlng = new google.maps.LatLng(split[0], split[1]);     
            marker = new google.maps.Marker({
                                              position: latlng,
                                              title:"ID:"+res[1],
                                              idcelular:counterCelulares,
                                              immei:res[1],
                                              icon: {
                                                url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                                                    }
                                            });
            marker.setValues({type: "point", id: res[1]});
            marker.setMap(celular);
            celular.setCenter(marker.getPosition()) 
            markersCelulares.push(marker);
      }
    }
     
      if (res[0]=="CENTRAL")
    {
     
     
      if (res[1]=="PANICO" || res[1]=="SALUD" || res[1]=="ALERTA")
      {
       
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

    function grabar()
    { 
      
    }
    
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
