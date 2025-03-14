function addUserChat() {
    var url = "../clientes/?event=lista_json_chat";

    $.getJSON(url, { format: "json" })
        .done(function (data) {
            let userListHTML = ""; // Acumulador de HTML

            data.forEach(element => {
                // Validar datos
                const nombre = element.nombre || "";
                const apellido = element.apellido || "";
                const telefono = element.telefono || "";
                const idcliente = element.idcliente || "";

                // Determinar el nombre a mostrar
                const displayName = (nombre === "" && apellido === "") 
                    ? telefono 
                    : `${nombre}, ${apellido}`;

                // Construir el HTML usando plantillas literales
                const userHTML = `
                    <div class="row sideBar-body" style="display:block" celular="${idcliente}" nombre="${nombre}" apellido="${apellido}">
                        <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                                <img src="../img/icons/person.png">
                            </div>
                        </div>
                        <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <span class="name-meta" onclick="Chatear('${idcliente}', '${telefono}')">
                                    ${displayName}
                                </span>
                            </div>
                        </div>
                    </div>
                `;

                userListHTML += userHTML; // Acumular el HTML
            });

            // Añadir todos los usuarios al DOM de una sola vez
            $('#usuario_chat_disponibles').html(userListHTML);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar los datos:", textStatus, errorThrown);
        });
}
$(document).ready(function() {
    $(".heading-compose").click(function() {
        $(".side-two").css({ "left": "0" });
    });

    $(".newMessage-back").click(function() {
        $(".side-two").css({ "left": "-100%" });
    });

    $("#draggable").draggable();
    $("#draggable").css({'top': -810, 'left': 1020});

    $(document).keypress(function(event) {
        if (event.which == '13') {
            SendMensaje();
            event.preventDefault();
        }
    });
});

function CloseChat() {
    $("#chat").hide();
}

/*function SendMensaje() {
    let mensaje = $("#mensaje").val();
    if (mensaje.trim() !== "") {
        let nuevoMensaje = `<div class="message-main-sender"><div class="sender">${mensaje}</div></div>`;
        $("#mensajesChat").append(nuevoMensaje);
        $("#mensaje").val('');
    }
}*/
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
// Función para formatear la fecha y hora
function formatDate(timestamp) {
    let d = new Date(timestamp);
    let hours = d.getHours().toString().padStart(2, '0');
    let minutes = d.getMinutes().toString().padStart(2, '0');
    let seconds = d.getSeconds().toString().padStart(2, '0');
    return `${hours}:${minutes}:${seconds}`;
}

function cargarMensajes(mensajes) {
    let contenedorMensajes = $('#mensajesChat');
    contenedorMensajes.empty(); // Limpiar mensajes previos

    mensajes.forEach(mensaje => {
        let fecha = new Date(mensaje.timestamp);
        let hora = fecha.getHours().toString().padStart(2, '0');
        let minutos = fecha.getMinutes().toString().padStart(2, '0');
        let segundos = fecha.getSeconds().toString().padStart(2, '0');
        let horaFormato = `${hora}:${minutos}:${segundos}`;

        let htmlMensaje;
        if (mensaje.sender === "user") {
            // Mensaje enviado por el usuario
            htmlMensaje = `
                <div class='col-sm-12 message-main-sender'>
                    <div class='sender'>
                        <div class='message-text'>${mensaje.mensaje}</div>
                        <span class='message-time pull-right'>${horaFormato}</span>
                    </div>
                </div>`;
        } else if (mensaje.sender === "assistant") {
            // Mensaje enviado por el asistente
            htmlMensaje = `
                <div class='col-sm-12 message-main-receiver'>
                    <div class='receiver'>
                        <div class='message-text'>${mensaje.mensaje}</div>
                        <span class='message-time pull-right'>${horaFormato}</span>
                    </div>
                </div>`;
        }

        contenedorMensajes.append(htmlMensaje);
    });

    // Hacer scroll automático hacia el último mensaje
    contenedorMensajes.scrollTop(contenedorMensajes[0].scrollHeight);
}
var idCliente=0;
// Función para obtener los mensajes desde PHP y cargarlos en el chat
function obtenerMensajes(id) {
    idCliente=id;
    $.ajax({
        url: `/clientes/?event=chats&idcliente=${id}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log("Mensajes recibidos:", response);
            cargarMensajes(response);
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener mensajes:", error);
        }
    });
}
function setTelefono(telefono) {
    // Seleccionar el div con id "conversation"
    let conversationDiv = document.getElementById("conversation");

    // Asignar el número de teléfono al atributo "telefono"
    if (conversationDiv) {
        conversationDiv.setAttribute("telefono", telefono);
    } else {
        console.error("❌ No se encontró el elemento con id 'conversation'");
    }
}

// Función que se ejecuta cuando se hace clic en "Chatear"
function Chatear(idCliente,telefono) {
    setTelefono(telefono); 
    $('#chat').show(); // Mostrar la ventana de chat
    obtenerMensajes(idCliente);
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
function SendMensaje() {
    // Validar que idCliente y telefono tengan valores válidos
    // Obtener el elemento por su ID
const conversationDiv = document.getElementById("conversation");

// Obtener el valor del atributo "telefono"
const telefono = conversationDiv.getAttribute("telefono");
    if (!idCliente || !telefono) {
        console.error("Error: idCliente o telefono no están definidos. telefono:"+telefono+"-"+idCliente);
        return;
    }

    // Obtener el mensaje del input
    const mensaje = $("#mensaje").val().trim();

    // Validar que el mensaje no esté vacío
    if (!mensaje) {
        console.error("Error: El mensaje no puede estar vacío.");
        return;
    }

    // Deshabilitar el botón de enviar
    $("#btnEnviar").prop("disabled", true);

    // Realizar la solicitud AJAX
    $.ajax({
        url: `/clientes/?event=sender`, // Endpoint del servidor PHP
        type: 'POST', // Usar POST para enviar datos
        dataType: 'json',
        data: {
            idcliente: idCliente,
            telefono: telefono,
            mensaje: mensaje
        },
        success: function (response) {
            console.log("Respuesta del servidor:", response);

            // Obtener la hora actual en formato HH:MM:SS
            const d = new Date();
            const hora = `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}:${String(d.getSeconds()).padStart(2, '0')}`;

            // Construir el HTML del mensaje usando plantillas literales
            const mensajeHTML = `
                <div class="col-sm-12 message-main-sender">
                    <div class="sender">
                        <div class="message-text">${mensaje}</div>
                        <span class="message-time pull-right">${hora}</span>
                    </div>
                </div>
            `;

            // Añadir el mensaje al DOM
            $('#mensajesChat').append(mensajeHTML);

            // Limpiar el input y hacer scroll al final del chat
            $("#mensaje").val('');
            scrollToBottom();
        },
        error: function (xhr, status, error) {
            console.error("Error al enviar el mensaje:", error);
        },
        complete: function () {
            // Habilitar el botón de enviar cuando la solicitud se complete
            $("#btnEnviar").prop("disabled", false);
        }
    });
}