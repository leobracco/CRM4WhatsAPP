$(document).on('click', '#generateToken', function() {
    //alert(this.value);
    $.get("/token/?event=generateToken")
        .done(function(data) {
            $("#token").val(data.resultado);
        });

});
$(document).on('click', '#saveToken', function() {
    //alert(this.value);
    $.get("/token/?event=grabar", { token: $("#token").val() })
        .done(function(data) {
            $("#info-token").html(data.texto);
            $("#info-token").show();
        });

});