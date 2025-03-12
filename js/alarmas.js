

var url = "../alarmas/?event=view";
var speeds = [];
var temp = [];
var arrayEventos = new Array;
var arrayIdSensorVal = new Array;


$(document).ready(function() {


$.ajax({
   
   
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
        console.log("ANTES ID sensor" + data.rows[i]['doc'].id);
       
        arrayEventos.push(
            {
                id:data.rows[i]['doc'].id,
                version:data.rows[i]['doc'].version,
                
                herramientas:"<button type='button' id='button_update'>Update</button>&nbsp<button type='button' id='button_test'>Test</button>&nbsp<button type='button' id='button_edit'>Editar</button>"

        })
        
        


    }
    
    table = $('#tabla').DataTable({
        data: arrayEventos,
        "language": {
            "url": "../js/Spanish.json"
        },
        "pagingType": "simple",
        "columns": [{
            "data": "id"
        }, {
            "data": "version"
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
