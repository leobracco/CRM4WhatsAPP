L.mapbox.accessToken = 'pk.eyJ1IjoicXVvZGlpIiwiYSI6ImNrbzBqd3NmdTBkdjkyb21rOHM1MjR4dHIifQ.RX7ilY2PuoFDRmWq1NjBSg';
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
var lastBoton=1;
var lastusername=1;
var JsonData={"geometry": {"type": "Point", "coordinates": ['0','0']}, "type": "Feature", "properties": {}};


function addUserChat()
{
    var url = "../clientes/?event=lista_json_chat";
    $.getJSON(url, { format: "json" }).
    done(function(data)
    {
        data.forEach(element =>
            {
                var addUser;
                addUser="<div class='row sideBar-body' style='display:block' celular='"+element.telefono+"' nombre='"+element.nombre+"' celular='"+element.apellido+"'>";
                addUser+="<div class='col-sm-3 col-xs-3 sideBar-avatar'>";
                addUser+="<div class='avatar-icon'>";
                addUser+="<img src='../img/icons/person.png'>";
                addUser+="</div></div>";
                addUser+="<div class='col-sm-9 col-xs-9 sideBar-main'>";
                addUser+="<div class='row'>";
                addUser+="<div class='col-sm-8 col-xs-8 sideBar-name'>";
                if (element.nombre=='' && element.apellido=='')
                addUser+="<span class='name-meta' onclick=Chatear('"+element.telefono+"','"+element.nombre+"','"+element.apellido+"')>"+element.telefono;
                else
                addUser+="<span class='name-meta' onclick=Chatear('"+element.telefono+"','"+element.nombre+"','"+element.apellido+"')>"+element.nombre+","+element.apellido;
                addUser+="</span></div></div></div></div></div>";
                $('#usuario_chat_disponibles').append(addUser);
            });
        });
}
$(document).ready(function() {
    addUserChat()
    table = $('#tabla').DataTable({
        "language": {
            "url": "../js/Spanish.json"
        },
        "order": [[ 3, "desc" ]],
        "pagingType": "simple"
    });

   
});
function BuscarPersona()
{
    $.each($('.row sideBar-body'), function(index, value) {
        console.log(index + ':' + $(value).text());
      });
}


function Chatear(username,nombre,apellido)
{
    //console.log(celular);
    $("#conversation").attr("username",username);
    $("#chatUsuario").html(nombre+","+apellido);
}
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
        onFailure: function(message) {


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
    mqtt.subscribe(topic+"/#", { qos: 1 });
    

}

function onConnectionLost(response) {
    Lobibox.notify('error', {
        sound: false,
        size: 'mini',
        msg: 'Se desconecto del server.'
    });
    setTimeout(MQTTconnect, reconnectTimeout);


};
var obj;

function onMessageArrived(message) {

    var topic = message.destinationName;
    var payload = message.payloadString;
    var res = topic.split("/");

    //var split = payload.split(":");
    //console.log("Entra con topic:"+topic)
    //console.log(split[0] + "-" + split[1]);
    obj = JSON.parse(message.payloadString); 
        if (res[4] == "gps") {
	    console.log("Entra en gps")
            if (obj.latitud)
            {
		JsonData={"geometry": {"type": "Point", "coordinates": [obj.longitud,obj.latitud]}, "type": "Feature", "properties": {}};
		eventos.getSource(obj.celular).setData(JsonData)
            eventos.flyTo({center: JsonData.geometry.coordinates,speed: 0.5});
	    console·log("Entra sale de lat obj")
            console.log(message.payloadString);
        }


        }
        //quodii/munitsas/pignus/cloud/GPS
    if (res[4] == "MENSAJE") {
	console.log("Entra en mensaje")
        obj = JSON.parse(message.payloadString); 
            mensajeRecibido(obj);
        
    }
    if (res[4] == "boton")
    {
	console.log("Entra en boton")
        obj = JSON.parse(message.payloadString); 
            var icono;
            var mensajeBoton;
            var mensajeIcon;
            var clase;
            switch (obj.boton) {
                case "SALUD":
                    icono = "hospital";
                    clase="error";
                    mensajeBoton="Salud";
                    mensajeIcon="salud";
                    break;
                case "PANICO":
                    icono = "police";
                    clase="error";
                    mensajeBoton="Policia";
                    mensajeIcon="policia";
                    break;
                case "ALERTA":
                    clase="warning";
                    icono = "danger";
                    mensajeBoton="Emergencia";
                    mensajeIcon="alerta";
                    break;
                case "VDG":
                        clase="error";
                        icono = "danger";
                        mensajeBoton="Violencia de Genero";
                        mensajeIcon="vdg";
                        break;
                case "LUZ":
                        clase="success";
                        icono = "danger";
                        mensajeBoton="Encendio la Luz";
                        mensajeIcon="luz";
                        break;
                default:
                    clase="warning";
                    icono = "danger";
                    mensajeBoton="Mensaje sin indentificar";
                    mensajeIcon="warning";
                    break;
            }
	    console.log("Sale de l switch");
	    JsonData={"geometry": {"type": "Point", "coordinates": [obj.longitud,obj.latitud]}, "type": "Feature", "properties": {}};
                
	    eventos.getSource(obj.celular).setData(JsonData)
            eventos.flyTo({center: JsonData.geometry.coordinates,speed: 0.5});
            /*if (obj.latitud)
            {
		console·log("Entra sale de lat obj")
		
	    console·log("Entra sale de lat obj")
            }*/
            //console.log(lastusername+"!="+obj.username+" &&"+ lastBoton+"!="+ obj.boton)
            var d = new Date();

            table.row.add( [
                obj.nombre,
                obj.apellido,
                obj.celular,
                d.getHours()+":"+d.getMinutes()+":"+d.getSeconds(),
                mensajeBoton,
                "<button  onclick='GoMap("+obj.latitud+","+obj.longitud+")' class='btn btn-primary btn-block'>Ver</button>",
                "<button  onclick='Tomar()' class='btn btn-primary btn-block'>Tomar</button>",
            ] ).draw( false );
	     
               
            
        
    }
}
var UsuarioEnAlerta;
function Tomar()
{
    console.log(obj.nombre+","+obj.apellido+":"+obj.username);
    $("#conversation").attr("username",obj.username);
    $("#chatUsuario").html(obj.nombre+","+obj.apellido);
    $("#chat").show();
    $( "#chat" ).draggable();
    $( "#chat" ).css({'top': -560, 'left' : -350})
    //mqtt.send("quodii/"+username+"/mobile/recibido",JSON.stringify({mensaje:'Fue recibido su mensaje, un operador esta con usted!'}))

}



function Leonardo()
{
    //console.log(obj.nombre+","+obj.apellido+":"+obj.username);
    $("#conversation").attr("username","lbracco");
    $("#chatUsuario").html("Leonardo,Bracco");
    

}
function OpenChat()
{
    $("#chat").show();
    $( "#chat" ).draggable();
    $( "#chat" ).css({'top': -560, 'left' : -350})
}
function CloseChat()
{
    $("#chat").hide();
}

function SendMensaje()
{
    var d = new Date();
    
    mqtt.send("quodii/"+$( "#conversation" ).attr("username")+"/mobile/recibido",JSON.stringify({mensaje:$( "#mensaje" ).val()}))  
    $('#mensajesChat').append("<div class='col-sm-12 message-main-sender'><div class='sender'><div class='message-text'>"+$( "#mensaje" ).val()+"</div><span class='message-time pull-right'>"+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds()+"</span></div></div>");
    $( "#mensaje" ).val('');
    scrollToBottom();
}
function mensajeRecibido(obj)
{
    var d = new Date();
  
    console.log("Lllegal aalsl lsa:"+obj.mensaje)
    $('#mensajesChat').append("<div class='col-sm-12 message-main-receiver'><div class='receiver'><div class='message-text'>"+obj.mensaje+"</div><span class='message-time pull-right'>"+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds()+"</span></div></div>");
      scrollToBottom();
}
function scrollToBottom() {
    $("#conversation").scrollTop($("#conversation").outerHeight());
  }
function deleteMarker(markerId) {

    for (var i = 0; i < markersEventos.length; i++) {
        if (markersEventos[i].idmark === markerId) {

            markersEventos[i].setMap(null);
        }
    }
}
function GoMap(lat,long)
{
    
    console.log("Que dice?:"+lat+"-"+long)
    
    if(lat && long)
    eventos.fitBounds([[lat,long]]);
}

