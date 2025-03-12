
var url = "../mapas/?event=view";

var geojson = {
    "name":"NewFeatureType",
    "type":"FeatureCollection",
    "features":[{
        "type":"Feature",
        "geometry":{
            "type":"LineString",
            "coordinates":[]
        },
        "properties":null
    }]
};


var speeds = [];
var temp = [];
var arrayEventos = new Array;
var arrayIdSensorVal = new Array;
mapboxgl.accessToken = 'pk.eyJ1IjoicXVvZGlpIiwiYSI6ImNrbzBqd3NmdTBkdjkyb21rOHM1MjR4dHIifQ.RX7ilY2PuoFDRmWq1NjBSg';
var map = new mapboxgl.Map({
container: 'map',
style: 'mapbox://styles/mapbox/dark-v10',
center: [-60.2484336,-38.379956],
zoom: 10
});
 

$(document).ready(function() {


    $.ajax({
        // En data puedes utilizar un objeto JSON, un array o un query string
     
        //Cambiar a type: POST si necesario
        type: "GET",
        // Formato de datos que se espera en la respuesta
        dataType: "json",
        // URL a la que se enviará la solicitud Ajax
        url: url,
    })
    .done(function(data, textStatus, jqXHR) {
        if (console && console.log) {
            console.log("La solicitud se ha completado correctamente.");
        }
    
        //console.log("TAMAÑO:" + data.rows.length)
    
    
    
        for (var i = 0; i < data.rows.length; i++) {
            
            if (data.rows[i]['doc'].latitud && data.rows[i]['doc'].longitud)
            {
            geojson.features[0].geometry.coordinates.push([data.rows[i]['doc'].longitud,data.rows[i]['doc'].latitud]);
            console.log("ANTES ID sensor" + data.rows[i]['doc']._id);
            }
            
            
    
    
        }
 
        Mapa();
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        if (console && console.log) {
            console.log("La solicitud a fallado: " + textStatus);
        }
    })
    
    
    
    
        
    
    
    });
    
function Mapa()
{
    map.on('load', function () {
        map.addSource('earthquakes', {'type': 'geojson','data':geojson});
        map.addLayer(
        {
    'id': 'earthquakes-heat',
    'type': 'heatmap',
    'source': 'earthquakes',
    'maxzoom': 9,
    'paint': {
    // Increase the heatmap weight based on frequency and property magnitude
    'heatmap-weight': [
    'interpolate',
    ['linear'],
    ['get', 'mag'],
    0,
    0,
    6,
    1
    ],
    // Increase the heatmap color weight weight by zoom level
    // heatmap-intensity is a multiplier on top of heatmap-weight
    'heatmap-intensity': [
    'interpolate',
    ['linear'],
    ['zoom'],
    0,
    1,
    9,
    3
    ],
    // Color ramp for heatmap.  Domain is 0 (low) to 1 (high).
    // Begin color ramp at 0-stop with a 0-transparancy color
    // to create a blur-like effect.
    'heatmap-color': [
    'interpolate',
    ['linear'],
    ['heatmap-density'],
    0,
    'rgba(33,102,172,0)',
    0.2,
    'rgb(103,169,207)',
    0.4,
    'rgb(209,229,240)',
    0.6,
    'rgb(253,219,199)',
    0.8,
    'rgb(239,138,98)',
    1,
    'rgb(178,24,43)'
    ],
    // Adjust the heatmap radius by zoom level
    'heatmap-radius': [
    'interpolate',
    ['linear'],
    ['zoom'],
    0,
    2,
    9,
    20
    ],
    // Transition from heatmap to circle layer by zoom level
    'heatmap-opacity': [
    'interpolate',
    ['linear'],
    ['zoom'],
    7,
    1,
    9,
    0
    ]
    }
    },
    'waterway-label'
    );
     
    map.addLayer(
    {
    'id': 'earthquakes-point',
    'type': 'circle',
    'source': 'earthquakes',
    'minzoom': 7,
    'paint': {
    // Size circle radius by earthquake magnitude and zoom level
    'circle-radius': [
    'interpolate',
    ['linear'],
    ['zoom'],
    7,
    ['interpolate', ['linear'], ['get', 'mag'], 1, 1, 6, 4],
    16,
    ['interpolate', ['linear'], ['get', 'mag'], 1, 5, 6, 50]
    ],
    // Color circle by earthquake magnitude
    'circle-color': [
    'interpolate',
    ['linear'],
    ['get', 'mag'],
    1,
    'rgba(33,102,172,0)',
    2,
    'rgb(103,169,207)',
    3,
    'rgb(209,229,240)',
    4,
    'rgb(253,219,199)',
    5,
    'rgb(239,138,98)',
    6,
    'rgb(178,24,43)'
    ],
    'circle-stroke-color': 'white',
    'circle-stroke-width': 1,
    // Transition from heatmap to circle layer by zoom level
    'circle-opacity': [
    'interpolate',
    ['linear'],
    ['zoom'],
    7,
    0,
    8,
    1
    ]
    }
    },
    'waterway-label'
    );
    });
}
