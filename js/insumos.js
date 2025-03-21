$(document).ready(function() {

    table = $('#tabla').DataTable({
        "ajax": '?event=lista_json',
        "dom": '<"toolbar">frtip',
        "language": {
            "url": "../js/Spanish.json"
        },
        
        "initComplete": function(settings, json) {
            $("div.toolbar").html("<div class='showback'><button type ='button' id='editar' class='btn btn-theme03' >Nuevo</button></div>");
        },
        "pagingType": "simple",
        "columns": [{
            "data": "nombre"
        }, {
            "data": "stock"
        }, {
            "data": "costo"
        }, {
            "data": "mayorista"
        }, {
            "data": "herramientas"
        }]
    });
    $(document).on('click', '#editar', function() {
        $.get("/insumos/?event=form", { idcliente: this.value })
            .done(function(data) {
                console.log("Respuesta del servidor:", data); // ✅ Verifica en consola los datos recibidos
    
                var infoModal = $('#modal');
    
                if (data && typeof data === 'object') {
                    // Construcción del formulario con los datos del cliente
                    var formHTML = `
                        <form id="formEditarCliente">
                            <input type="hidden" id="idcliente" name="idcliente" value="${data.idcliente}">
                            
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="${data.nombre}">
                            </div>
                            
                            <div class="form-group">
                                <label for="apellido">Apellido:</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="${data.apellido}">
                            </div>
    
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="${data.email}">
                            </div>
    
                            <div class="form-group">
                                <label for="telefono">Teléfono:</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="${data.telefono}">
                            </div>
    
                            <div class="form-group">
                                <label for="direccion">Dirección:</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="${data.direccion}">
                            </div>
    
                            <button type="button" class="btn btn-primary" id="guardarCambios">Guardar Cambios</button>
                        </form>
                    `;
    
                    infoModal.find('.modal-body').html(formHTML);
                } else {
                    infoModal.find('.modal-body').html('<p>Error al cargar los datos.</p>');
                }
    
                infoModal.modal('show');
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud:", textStatus, errorThrown);
                $('#modal').find('.modal-body').html('<p>Error al obtener los datos.</p>').modal('show');
            });
    });
    
    $(document).on('click', '#guardarCambios', function() {
        var formData = $('#formEditarCliente').serialize(); // Serializa el formulario
    
        $.post("/insumos/?event=grabar", formData)
            .done(function(response) {
                console.log("Respuesta del servidor:", response);
    
                if (response.estado === 1) {
                    alert("Cliente actualizado correctamente.");
                    $('#modal').modal('hide'); // Cierra el modal
                    location.reload(); // Refresca la página para ver los cambios
                } else {
                    alert("Error al actualizar el cliente: " + response.texto);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al guardar:", textStatus, errorThrown);
                alert("Error al guardar los cambios.");
            });
    });
    
    
    $(document).on('click', '#borrar', function() {
        //alert(this.value);
        $.get("/insumos/?event=borrar", { idcliente: this.value })
            .done(function(data) {
                table.ajax.reload();
            });

    });


});