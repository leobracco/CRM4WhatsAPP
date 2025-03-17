
function changeTo(id) {
    console.log($(this).attr("href"))
    $.get("/cuentas/?event=changeTO", { idcuenta: id })
        .done(function(datero) {
            //$('#accounts').remove();
            $.get("/cuentas/?event=lista_inicio_json", { idcuenta: id })
                .done(function(data) {
                    // console.log("RESULT:" + data.resultado);
                    var nombre;
                    var idcheck = "check-" + id;
                    $("#accounts li").each(function(index) {

                        if ($("#" + $(this).attr("identi")).attr("class") != "yellow")
                            $("#" + $(this).attr("identi")).removeClass("fa fa-check");
                        if ($(this).attr("identi") == idcheck)
                            nombre = $(this).attr("name");


                    });
                    $("#check-" + id).addClass("fa fa-check");
                    $("#ref-account").text(nombre);


                });
        });
}


function SetCenter()
{
    map.addControl(geolocate);
    eventos.on('geolocate', function(e) {
        var lon = e.coords.longitude;
        var lat = e.coords.latitude
        var position = [lon, lat];
        console.log(position);
  });
}