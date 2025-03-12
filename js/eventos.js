

var url = "../eventos/?event=view";
var speeds = [];
var temp = [];
var arrayEventos = new Array;
var arrayIdSensorVal = new Array;


$(document).ready(function() {


$.ajax({
    // En data puedes utilizar un objeto JSON, un array o un query string
    data: {
        "cuenta": "muniposadas",
        "action": "PANICO"
    },
    //Cambiar a type: POST si necesario
    type: "GET",
    // Formato de datos que se espera en la respuesta
    dataType: "json",
    // URL a la que se enviará la solicitud Ajax
    url: url,
})
.done(function(data, textStatus, jqXHR) {
    if (console && console.log) {
        console.log("La solicitud se ha completado correctamente." + data.rows);
    }

    console.log("TAMAÑO:" + data.rows.length)



    for (var i = 0; i < data.rows.length; i++) {
        //console.log("ANTES ID sensor" + data.rows[i]['doc'].idsensor);
        if (data.rows[i]['doc'].boton)
        arrayEventos.push(
            {
                nombre:data.rows[i]['doc'].nombre,
                apellido:data.rows[i]['doc'].apellido,
                celular:data.rows[i]['doc'].celular,
                evento:data.rows[i]['doc'].boton,
                herramientas:"Sin Permiso"

        })
        
        


    }
    
    table = $('#tabla').DataTable({
        data: arrayEventos,
        "language": {
            "url": "../js/Spanish.json"
        },
        "pagingType": "simple",
        "columns": [{
            "data": "nombre"
        }, {
            "data": "apellido"
        }, {
            "data": "celular"
        }, {
            "data": "evento"
        }, {
            "data": "herramientas"
        }]
    });
    
})
.fail(function(jqXHR, textStatus, errorThrown) {
    if (console && console.log) {
        console.log("La solicitud a fallado: " + textStatus);
    }
})




    


});
$(document).ready(function() {
    $('#example').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
} );
