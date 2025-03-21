$(document).ready(function() {
    const ProductoManager = {
        init: function() {
            this.initAutocomplete();
            this.initDataTable();
            this.initEventListeners();
            this.loadTiposProducto();
        },

        initAutocomplete: function() {
            $("#nombreInsumo").autocomplete({
                source: (request, response) => this.fetchInsumos(request, response),
                minLength: 2,
                select: (event, ui) => this.handleInsumoSelect(event, ui)
            });
        },

        fetchInsumos: function(request, response) {
            $.ajax({
                url: "/insumos/?event=listado&search=" + request.term+"&idproducto="+$("#idproducto").val(),
                dataType: "json",
                success: (data) => response(this.mapInsumos(data.data))
            });
        },

        mapInsumos: function(data) {
            return $.map(data, (item) => ({
                label: item.nombre,
                value: item.nombre,
                id: item.id,
                costo: item.costo
            }));
        },

        handleInsumoSelect: function(event, ui) {
            $("#idinsumo").val(ui.item.id);
            $("#costoInsumo").val(parseFloat(ui.item.costo) || 0);
        },

        initDataTable: function() {
            this.table = $('#tabla').DataTable({
                ajax: '?event=lista_json',
                dom: '<"toolbar">frtip',
                language: { url: "../js/Spanish.json" },
                pagingType: "simple",
                columns: [
                    { data: "nombre" },
                    { data: "precio_minorista" },
                    { data: "precio_mayorista" },
                    { data: "stock" },
                    { data: "herramientas" }
                ],
                initComplete: (settings, json) => {
                    $("div.toolbar").html("<div class='showback'><button type='button' id='new' class='btn btn-theme03'>Nuevo</button></div>");
                }
            });
        },

        initEventListeners: function() {
            $(document).on('click', '#editar', (e) => this.handleEditClick(e));
            $(document).on('click', '#new', () => $('#modal').modal('show'));
            $(document).on('click', '#guardarCambios', () => this.handleSaveClick());
            $(document).on('click', '#borrar', (e) => this.handleDeleteClick(e));
            $('#productoTabs a').on('click', (e) => this.handleTabClick(e));
            $('#modal').on('shown.bs.modal', () => $('#productoTabs a:first').tab('show'));
            document.getElementById("agregarInsumo").addEventListener("click", () => this.handleAddInsumoClick());
            document.getElementById("tablaInsumos").addEventListener("click", (e) => this.handleDeleteInsumoClick(e));
        },

        handleEditClick: function(e) {
            // Usar e.currentTarget para obtener el botón, no el elemento interno (img)
            const idproducto = e.currentTarget.value; // Obtener el valor del atributo 'value'
            console.log("ID Producto:", idproducto); // Verificar en consola
        
            $.get("/productos/?event=form", { idproducto: idproducto })
                .done((data) => this.showEditModal(data))
                .fail((jqXHR, textStatus, errorThrown) => this.handleAjaxError(jqXHR, textStatus, errorThrown));
        },

        showEditModal: function(data) {
            cargarDatos(data);
            $('#modal').modal('show');
        },

        handleSaveClick: function() {
            const formData = $('#formEditarProducto').serialize();
            $.post("/productos/?event=grabar", formData)
                .done((response) => this.handleSaveResponse(response))
                .fail((jqXHR, textStatus, errorThrown) => this.handleAjaxError(jqXHR, textStatus, errorThrown));
        },

        handleSaveResponse: function(response) {
            if (response.estado === 1) {
                alert("Cliente actualizado correctamente.");
                $('#modal').modal('hide');
                location.reload();
            } else {
                alert("Error al actualizar el cliente: " + response.texto);
            }
        },

        handleDeleteClick: function(e) {
            $.get("/productos/?event=borrar", { idcliente: e.target.value })
                .done(() => this.table.ajax.reload())
                .fail((jqXHR, textStatus, errorThrown) => this.handleAjaxError(jqXHR, textStatus, errorThrown));
        },

        handleTabClick: function(e) {
            e.preventDefault();
            $(e.target).tab('show');
        },

        handleAddInsumoClick: function() {
            if ($("#idproducto").val() === '') {
                alert("Graba el producto primero");
            } else {
                const insumo = {
                    idinsumo: $("#idinsumo").val(),
                    idproducto: $("#idproducto").val(),
                    cantidad: parseFloat($("#cantidadInsumo").val()) || 0,
                    mayorista: $("#mayoristaInsumo").prop("checked")
                };

                $.post("/productos/?event=grabarInsumo", insumo)
                    .done((response) => this.handleAddInsumoResponse(response))
                    .fail((jqXHR, textStatus, errorThrown) => this.handleAjaxError(jqXHR, textStatus, errorThrown));
            }
        },

        handleAddInsumoResponse: function(response) {
            if (response.estado === 1) {
                alert("Insumo cargado correctamente.");
                cargarInsumos(response.insumos.insumos);
            } else {
                alert("Error al cargar el insumo: " + response.texto);
            }
        },

        handleDeleteInsumoClick: function(e) {
            if (e.target.classList.contains("eliminarInsumo")) {
                const row = e.target.closest("tr");
                const insumoId = row.querySelector("input[type='hidden'][id^='insumo-']").value;

                const insumo = {
                    idinsumo: insumoId,
                    idproducto: $("#idproducto").val()
                };

                $.post("/productos/?event=borrarInsumo", insumo)
                    .done((response) => this.handleDeleteInsumoResponse(response))
                    .fail((jqXHR, textStatus, errorThrown) => this.handleAjaxError(jqXHR, textStatus, errorThrown));
            }
        },

        handleDeleteInsumoResponse: function(response) {
            if (response.estado === 1) {
                alert("Insumo borrado correctamente.");
                cargarInsumos(response.insumos.insumos);
            } else {
                alert("Error al borrar el insumo: " + response.texto);
            }
        },

        loadTiposProducto: function() {
            $.ajax({
                url: "/tipos/?event=json",
                type: "GET",
                dataType: "json",
                success: (response) => this.populateTiposProducto(response),
                error: (xhr, status, error) => this.handleAjaxError(xhr, status, error)
            });
        },

        populateTiposProducto: function(response) {
            if (response.data && response.data.length > 0) {
                const select = $("#tipoProducto");
                select.empty().append('<option value="">-- Seleccionar Tipo --</option>');
                response.data.forEach((item) => select.append(`<option value="${item.idtipo}">${item.nombre}</option>`));
            } else {
                console.log("No se encontraron tipos de productos.");
            }
        },

        handleAjaxError: function(jqXHR, textStatus, errorThrown) {
            console.error("Error en la solicitud:", textStatus, errorThrown);
            alert("Error en la solicitud. Por favor, inténtalo de nuevo.");
        }
    };

    ProductoManager.init();
});

// Funciones externas (podrían ser movidas a un módulo separado)
function cargarDatos(data) {
    document.getElementById("idproducto").value = data.idproducto || "";
    document.getElementById("nombre").value = data.nombre || "";
    document.getElementById("stock").value = data.stock || 0;
    document.getElementById("tipoProducto").value = data.idtipo || 0;
    document.getElementById("markupMayorista").value = data.markupMayorista || 0;
    document.getElementById("markup").value = data.markup || 0;
    document.getElementById("precioMinorista").value = (data.insumos.precio_minorista || 0).toFixed(2);
    document.getElementById("precioMayorista").value = (data.insumos.precio_mayorista || 0).toFixed(2);
    cargarInsumos(data.insumos.insumos || []);
}

function cargarInsumos(insumos) {
    const tbody = document.getElementById("tablaInsumos");
    tbody.innerHTML = "";
    insumos.forEach((insumo, index) => tbody.appendChild(crearFilaInsumo(insumo, index)));
    calcularPrecios();
}

function calcularPrecios() {
    let totalCostoMinorista = 0;
    let totalCostoMayorista = 0;

    $("#tablaInsumos tr").each(function () {
        const row = $(this);
        const cantidad = parseFloat(row.find(".cantidad").val()) || 0;
        const costo = parseFloat(row.find(".costo").val()) || 0;
        const markup = parseFloat($("#markup").val()) || 0;
        const markupMayorista = parseFloat($("#markupMayorista").val()) || 0;
        const esMayorista = row.find(".mayorista").prop("checked");

        const costoTotal = cantidad * costo;
        row.find(".costoTotal").text(costoTotal.toFixed(2));

        const precioMinorista = (markup < 100) ? (costoTotal / (100 - markup)) * 100 : 0;
        const precioMayorista = (markupMayorista < 100) ? (costoTotal / (100 - markupMayorista)) * 100 : 0;

        totalCostoMinorista += precioMinorista;
        if (esMayorista) {
            totalCostoMayorista += precioMayorista;
        }
    });

    $("#precioMinorista").val(totalCostoMinorista.toFixed(2));
    $("#precioMayorista").val(totalCostoMayorista.toFixed(2));
}

function crearFilaInsumo(insumo = {}, index) {
    const row = document.createElement("tr");
    row.innerHTML = `
        <input type="hidden" id="insumo-${insumo.id || ''}" class="id" value="${insumo.id || ''}">
        <td><input type="text" class="form-control input-insumo nombre" value="${insumo.nombre || ''}" data-index="${index}"></td>
        <td><input type="number" class="form-control cantidad" value="${insumo.cantidad || 0}" step="0.01" oninput="calcularPrecios()"></td>
        <td><input type="number" class="form-control costo" value="${insumo.costo_unitario || 0}" step="0.01" disabled></td>
        <td><input type="checkbox" class="mayorista" ${insumo.mayorista ? 'checked' : ''} oninput="calcularPrecios()"></td>
        <td class="costoTotal">${((insumo.cantidad || 0) * (insumo.costo_unitario || 0)).toFixed(2)}</td>
        <td><button type="button" class="btn btn-danger btn-sm eliminarInsumo">🗑</button></td>
    `;
    return row;
}