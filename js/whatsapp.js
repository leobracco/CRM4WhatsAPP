var idCliente = 0;
var ultimaActualizacion = 0;
// 📌 Cargar lista de usuarios y ordenar por mensaje más reciente
function actualizarUsuarios() {
    $.getJSON("../clientes/?event=lista_json_chat", function (data) {
        let userListHTML = "";
        
        // Ordenar usuarios por timestamp (más recientes arriba)
        data.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

        data.forEach(element => {
            const nombre = element.nombre || "";
            const apellido = element.apellido || "";
            const telefono = element.telefono || "";
            const idcliente = element.idcliente || "";
            const displayName = (nombre === "" && apellido === "") ? telefono : `${nombre}, ${apellido}`;

            userListHTML += `
    <div class="row sideBar-body" style="display:block" celular="${idcliente}" nombre="${nombre}" apellido="${apellido}" onclick="Chatear(this, '${idcliente}', '${telefono}')">
        <div class="col-sm-3 col-xs-3 sideBar-avatar">
            <div class="avatar-icon">
                <img src="../img/icons/person.png">
            </div>
        </div>
        <div class="col-sm-9 col-xs-9 sideBar-main">
            <div class="row">
                <span class="name-meta">
                    ${displayName}
                </span>
            </div>
        </div>
    </div>`;
        });

        $('#usuario_chat_disponibles').html(userListHTML);
    });
}

// 📌 Formatear la fecha y hora
function formatDate(timestamp) {
    let d = new Date(timestamp);
    return `${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}:${d.getSeconds().toString().padStart(2, '0')}`;
}

// 📌 Cargar mensajes en el chat
function cargarMensajes(mensajes) {
    let contenedorMensajes = $('#mensajesChat');
    let nuevosMensajes = "";

    mensajes.forEach(mensaje => {
        let horaFormato = formatDate(mensaje.timestamp);
        let contenidoMensaje = "";

        // Verificar si el mensaje es multimedia
        if (mensaje.tipo === 'image') {
            // Mostrar una imagen
            contenidoMensaje = `
                <div class="message-media">
                    <img src="/uploads/${mensaje.archivo}" alt="Imagen" class="img-responsive" style="max-width: 100%; height: auto;">
                </div>`;
        } else if (mensaje.tipo === 'document') {
            // Mostrar un enlace para descargar el documento
            contenidoMensaje = `
                <div class="message-media">
                    <a href="/uploads/${mensaje.archivo}" target="_blank" class="btn btn-primary">
                        <i class="fa fa-download"></i> Descargar documento
                    </a>
                </div>`;
        } else if (mensaje.tipo === 'audio') {
            // Mostrar un reproductor de audio
            contenidoMensaje = `
                <div class="message-media">
                    <audio controls>
                        <source src="/uploads/${mensaje.archivo}" type="audio/mpeg">
                        Tu navegador no soporta la reproducción de audio.
                    </audio>
                </div>`;
        } else {
            // Mostrar un mensaje de texto normal
            contenidoMensaje = `<div class='message-text'>${mensaje.mensaje}</div>`;
        }

        // Construir el HTML del mensaje
        let htmlMensaje = `
            <div class='col-sm-12 message-main-${["assistant", "human"].includes(mensaje.sender) ? "sender" : "receiver"}'>
                <div class='${["assistant", "human"].includes(mensaje.sender) ? "sender" : "receiver"}'>
                    ${contenidoMensaje}
                    <span class='message-time pull-right'>${horaFormato}</span>
                </div>
            </div>`;

        nuevosMensajes += htmlMensaje;
    });

    // Actualizar el contenedor de mensajes
    contenedorMensajes.html(nuevosMensajes);

    // Hacer scroll al final del contenedor
    let contenedor = $('#conversation');
    contenedor.scrollTop(contenedor[0].scrollHeight);
}



function obtenerMensajes(id) {
    idCliente = id;

    // Si es la primera vez que abrimos el chat, inicializar su última actualización
    if (!ultimaActualizacion[idCliente]) {
        ultimaActualizacion[idCliente] = 0;
    }

    $.getJSON(`/clientes/?event=chats&idcliente=${idCliente}`, function (response) {
        let ultimoMensaje = response.length ? new Date(response[response.length - 1].timestamp).getTime() : 0;

        // Siempre cargar los mensajes al cambiar de chat
        if (ultimoMensaje !== ultimaActualizacion[idCliente]) {
            cargarMensajes(response);
            ultimaActualizacion[idCliente] = ultimoMensaje;
        }
    });
}

// 📌 Obtener datos del cliente y llenar el formulario
function obtenerDatosCliente(id) {
    idCliente = id;
    $.getJSON(`/clientes/?event=form&idcliente=${id}`, function (response) {
        $("#nombre").val(response.nombre);
        $("#apellido").val(response.apellido);
        $("#email").val(response.email);
        $("#telefono").val(response.telefono);
        $("#direccion").val(response.direccion);
    });
    
}

// 📌 Configurar el número de teléfono en el chat
function setTelefono(telefono) {
    document.getElementById("conversation").setAttribute("telefono", telefono);
}

// 📌 Modificar `Chatear()` para limpiar `ultimaActualizacion` y forzar la recarga de mensajes
function Chatear(element, idcliente, telefono) {
    setTelefono(telefono);
    if (!element) return;

    // Quitar la clase 'active-chat' de todos los chats antes de aplicar la nueva selección
    $(".sideBar-body").removeClass("active-chat");

    // Agregar la clase 'active-chat' solo al chat clickeado
    $(element).addClass("active-chat");
    $('#chat').show();
    obtenerDatosCliente(idcliente);

    // Resetear última actualización para que siempre cargue los mensajes desde cero
    ultimaActualizacion[idcliente] = 0;

    obtenerMensajes(idcliente);
    setTimeout(() => {
        let contenedorMensajes = $('#conversation');
        contenedorMensajes.scrollTop(contenedorMensajes.prop("scrollHeight"));
    }, 100); // Se puede ajustar el tiempo si es necesario
}

// 📌 Cerrar chat
function CloseChat() {
    $("#chat").hide();
}

// 📌 Enviar mensaje
function SendMensaje() {
    let telefono = document.getElementById("conversation").getAttribute("telefono");
    let mensaje = $("#mensaje").val().trim();

    if (!idCliente || !telefono || !mensaje) {
        console.error("Error: Datos incompletos para enviar mensaje.");
        return;
    }

    $("#btnEnviar").prop("disabled", true);

    $.ajax({
        url: `/clientes/?event=sender`,
        type: 'POST',
        dataType: 'json',
        data: { idcliente: idCliente, telefono: telefono, mensaje: mensaje },
        success: function (response) {
            let hora = formatDate(new Date());
            let mensajeHTML = `
                <div class="col-sm-12 message-main-sender">
                    <div class="sender">
                        <div class="message-text">${mensaje}</div>
                        <span class="message-time pull-right">${hora}</span>
                    </div>
                </div>`;

            $('#mensajesChat').append(mensajeHTML);
            $("#mensaje").val('');
            $("#mensajesChat").scrollTop($("#mensajesChat")[0].scrollHeight);
        },
        error: function (xhr, status, error) {
            console.error("Error al enviar el mensaje:", error);
        },
        complete: function () {
            $("#btnEnviar").prop("disabled", false);
        }
    });
}

// 📌 Guardar cliente
function guardarCliente() {
    let cliente = {
        idcliente: idCliente,
        nombre: $("#nombre").val(),
        apellido: $("#apellido").val(),
        email: $("#email").val(),
        telefono: $("#telefono").val(),
        direccion: $("#direccion").val()
    };

    $.ajax({
        url: "/clientes/?event=grabar",
        type: "POST",
        data: cliente,
        success: function () {
            alert("✅ Cliente actualizado correctamente.");
        },
        error: function (error) {
            console.error("Error al guardar:", error);
        }
    });
}

// 📌 Inicializar funciones cuando la página carga
$(document).ready(function () {
    actualizarUsuarios();
    setInterval(() => {
        actualizarUsuarios();
        if (idCliente) obtenerMensajes(idCliente);
    }, 5000); // Actualiza cada 5 segundos
    
    $('#tabla').DataTable({
        "language": { "url": "../js/Spanish.json" },
        "order": [[3, "desc"]],
        "pagingType": "simple"
    });

    $("#draggable").draggable().css({ 'top': -810, 'left': 1020 });

    $(document).keypress(function (event) {
        if (event.which == '13') {
            SendMensaje();
            event.preventDefault();
        }
    });
});
