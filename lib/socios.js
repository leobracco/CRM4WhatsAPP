$(function() {
    $(document).on('click', '#nuevo', function() {
        alert(this.value);

    });
    $(document).on('click', '#editar', function() {
        //alert(this.value);
        ConfirmDialog("Editar");

    });

    $(document).on('click', '#activar', function() {
        if (ConfirmDialog("Desea activar relamente al socio?")) {


            $.get("/socios/?event=activar", { idsocio: this.value })
                .done(function(data) {
                    table.ajax.reload();
                });

        }

    });
    $(document).on('click', '#desactivar', function() {
        if (ConfirmDialog("Desea desactivar relamente al socio?")) {


            $.get("/socios/?event=desactivar", { idsocio: this.value })
                .done(function(data) {
                    table.ajax.reload();
                });

        }

    });
    $(document).on('click', '#borrar', function() {
        alert(this.value);

    });
});


function ConfirmDialog(message) {

    return window.confirm(message)
};