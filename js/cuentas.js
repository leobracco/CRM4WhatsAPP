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
            "data": "username"
        }, {
            "data": "default"
        }, {
            "data": "usethis"
        }, {
            "data": "herramientas"
        }]
    });

    $(document).on('click', '#editar', function() {
        //alert(this.value);
        $.get("/cuentas/?event=form", { idcuenta: this.value })
            .done(function(data) {
                //$("#edicion").html();
                var infoModal = $('#modal');
                infoModal.find('.modal-body').html(data.resultado);
                infoModal.modal('show');
            });

    });
    $(document).on('click', '#borrar', function() {
        //alert(this.value);
        $.get("/cuentas/?event=borrar", { idcuenta: this.value })
            .done(function(data) {
                table.ajax.reload();
            });

    });
    $(document).on('click', '#setdefault', function() {
        //alert(this.value);
        $.get("/cuentas/?event=setdefault", { idcuenta: this.value })
            .done(function(data) {
                table.ajax.reload();
            });

    });
    $(document).on('click', '#usethis', function() {
        //alert(this.value);
        $.get("/cuentas/?event=usethis", { idcuenta: this.value })
            .done(function(data) {
                table.ajax.reload();
            });

    });

});