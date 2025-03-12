var username = false;
var email = false;
var celular = false;
var password = false;
var nombre = false;
var apellido = false;
var empresa = false;
var cuenta = false;

$("#cuenta-register").keyup(function() {
    this.value = this.value.toLowerCase();
    if (this.value.length > 4) {

        $.get("/register/?event=checkaccountEmpresa", {

                cuenta: $("#cuenta-register").val()
            })
            .done(function(data) {
                $("#valid-cuenta-register").removeClass();
                $("#valid-cuenta-register").html(data.resultado)
                $("#valid-cuenta-register").addClass(data.div);
                cuenta = data.valid;

            });
    } else {
        $("#valid-cuenta-register").removeClass();
        $("#valid-cuenta-register").html("Minimo 5 caracteres")
        $("#valid-cuenta-register").addClass("alert alert-danger");
    }

});
$("#username-register").keyup(function() {
    if (this.value.length > 4)
        $.get("/register/?event=checkaccountUser", {

            username: $("#username-register").val()
        })
        .done(function(data) {
            $("#valid-username-register").removeClass();
            $("#valid-username-register").html(data.resultado)
            $("#valid-username-register").addClass(data.div);
            username = data.valid;
        });
});
$("#email-register").keyup(function() {
    if (this.value.length > 3)
        $.get("/register/?event=checkemail", {

            email: $("#email-register").val()
        })
        .done(function(data) {
            $("#valid-email-register").removeClass();
            $("#valid-email-register").html(data.resultado)
            $("#valid-email-register").addClass(data.div);
            email = data.valid;
        });
});
$("#celular-register").keyup(function() {
    if (this.value.length > 7)
        $.get("/register/?event=checkcelular", {

            celular: $("#celular-register").val()
        })
        .done(function(data) {
            $("#valid-celular-register").removeClass();
            $("#valid-celular-register").html(data.resultado)
            $("#valid-celular-register").addClass(data.div);
            celular = data.valid;
        });
});
$("#password-register").keyup(function() {

    $.get("/register/?event=checkpassword", {

            password: $("#password-register").val()
        })
        .done(function(data) {
            $("#valid-password-register").removeClass();
            $("#valid-password-register").html(data.resultado)
            $("#valid-password-register").addClass(data.div);
            password = data.valid;
        });
});
$("#nombre-register").keyup(function() {
    if (this.value.length > 4) {
        $("#valid-nombre-register").removeClass();
        $("#valid-nombre-register").html("Nombre Correcto")
        $("#valid-nombre-register").addClass("alert alert-success");
        nombre = true;
    } else {
        $("#valid-nombre-register").removeClass();
        $("#valid-nombre-register").html("Debe contener 4 caracteres como minimo")
        $("#valid-nombre-register").addClass("alert alert-danger");
        nombre = false;
    }
});
$("#apellido-register").keyup(function() {
    if (this.value.length > 4) {
        $("#valid-apellido-register").removeClass();
        $("#valid-apellido-register").html("Apellido Correcto")
        $("#valid-apellido-register").addClass("alert alert-success");
        apellido = true;
    } else {
        $("#valid-apellido-register").removeClass();
        $("#valid-apellido-register").html("Debe contener 4 caracteres como minimo")
        $("#valid-apellido-register").addClass("alert alert-danger");
        apellido = false;
    }
});
$("#empresa-register").keyup(function() {
    if (this.value.length > 4) {
        $("#valid-empresa-register").removeClass();
        $("#valid-empresa-register").html("Empresa Correcta")
        $("#valid-empresa-register").addClass("alert alert-success");
        apellido = true;
    } else {
        $("#valid-empresa-register").removeClass();
        $("#valid-empresa-register").html("Debe contener 4 caracteres como minimo")
        $("#valid-empresa-register").addClass("alert alert-danger");
        apellido = false;
    }
});
$("#register").click(function() {

    $.post("/register/?event=register_web", {

            password: $("#password-register").val(),
            username: $("#username-register").val(),
            email: $("#email-register").val(),
            celular: $("#celular-register").val(),
            empresa: $("#empresa-register").val(),
            nombre: $("#nombre-register").val(),
            apellido: $("#apellido-register").val(),
            cuenta: $("#cuenta-register").val()
        })
        .done(function(data) {
            $("#valid-register").removeClass();
            $("#valid-register").html(data.resultado)
            $("#valid-register").addClass(data.div);

        });
});