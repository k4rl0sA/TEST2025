<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visualización de Predios - San Cristóbal</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-search@3.0.2/dist/leaflet-search.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }
    #map {
      height: 600px;
      margin-top: 20px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .search-input {
      width: 300px !important;
    }
    .legend {
      padding: 10px;
      background: white;
      border-radius: 5px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      line-height: 1.5;
    }
    .legend i {
      width: 18px;
      height: 18px;
      float: left;
      margin-right: 8px;
      opacity: 0.7;
    }
    h1 {
      color: #2c3e50;
    }
  </style>
</head>
<body>
  <h1>Visitas realizadas EBEH</h1>
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-search@3.0.2/dist/leaflet-search.min.js"></script>
  <script>
    // Datos del JSON (copiar el contenido completo de Pred_3_atenciones_mz.json aquí)
    const prediosData = {
      "displayFieldName": "",
      "fieldAliases": {
        "Id_geo": "Id_geo",
        "SUBRED": "SUBRED",
        "ZONA": "ZONA",
        "LOCALIDAD": "LOCALIDAD",
        "TERRITORIO": "TERRITORIO",
        "UPZ": "UPZ",
        "COD_BARRIO": "COD_BARRIO",
        "SECTOR_CATASTRAL_": "SECTOR CATASTRAL\n",
        "N_MANZANA": "N_MANZANA",
        "N_PREDIO": "N_PREDIO",
        "UNIDAD_HABITACIONAL": "UNIDAD_HABITACIONAL",
        "DIRECCION": "DIRECCION",
        "VEREDA": "VEREDA",
        "COORDENADA_X": "COORDENADA_X",
        "COORDENADA_Y": "COORDENADA_Y",
        "ESTRATO": "ESTRATO",
        "ESTADO_VISITA": "ESTADO_VISITA",
        "MOTIVO_ESTADO": "MOTIVO_ESTADO",
        "NOMBRE_GESTOR": "NOMBRE_GESTOR",
        "FECHA_CARACTERIZADO": "FECHA_CARACTERIZADO"
    },
       "features": [
        {
            "attributes": {
                "Id_geo": 658878,
                "SUBRED": "CENTRO ORIENTE",
                "ZONA": "1",
                "LOCALIDAD": "SAN CRISTÓBAL",
                "TERRITORIO": "CE043",
                "UPZ": "LA GLORIA",
                "COD_BARRIO": "40091",
                "SECTOR_CATASTRAL_": "1354",
                "N_MANZANA": "30",
                "N_PREDIO": "13",
                "UNIDAD_HABITACIONAL": "1",
                "DIRECCION": "KR 2H 37D 21 SUR",
                "VEREDA": "NULL",
                "COORDENADA_X": -74.099276852299994,
                "COORDENADA_Y": 4.5571093253799999,
                "ESTRATO": "2",
                "ESTADO_VISITA": "EFECTIVA",
                "MOTIVO_ESTADO": "N/A",
                "NOMBRE_GESTOR": "JENNIFER ALEXANDRA FONSECA ESCALANTE",
                "FECHA_CARACTERIZADO": 1741392000000,
            },
            "geometry": {
                "rings": [
                    [
                        [
                            -74.09922819399992,
                            4.5571123490000787
                        ],
                        [
                            -74.099255401999926,
                            4.5570654360000731
                        ],
                        [
                            -74.099281086999952,
                            4.5570804140000405
                        ],
                        [
                            -74.099302114999944,
                            4.5570926750000922
                        ],
                        [
                            -74.099325478999901,
                            4.5571063000000436
                        ],
                        [
                            -74.099310532999937,
                            4.5571321570000691
                        ],
                        [
                            -74.09930739999993,
                            4.5571375770000486
                        ],
                        [
                            -74.099298361999899,
                            4.5571532130000492
                        ],
                        [
                            -74.099253953999948,
                            4.5571273510000765
                        ],
                        [
                            -74.099245383999914,
                            4.5571223590000614
                        ],
                        [
                            -74.09922819399992,
                            4.5571123490000787
                        ]
                    ]
                ]
            }
        }
     ]
    };

    // Convertir el formato ArcGIS a GeoJSON estándar
    function convertToGeoJSON(arcgisJson) {
      return {
        type: "FeatureCollection",
        features: arcgisJson.features.map(feature => ({
          type: "Feature",
          properties: feature.attributes,
          geometry: {
            type: "Polygon",
            coordinates: feature.geometry.rings
          }
        }))
      };
    }

    // Crear mapa centrado en San Cristóbal, Bogotá
    const map = L.map('map').setView([4.5572, -74.0992], 17);

    // Añadir capa base (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Función para determinar el color según el estado de visita
    function getColorByEstado(estado) {
      switch(estado) {
        case 'EFECTIVA': return '#2ecc71'; // Verde
        case 'AUSENTE1': return '#f39c12'; // Naranja
        case 'AUSENTE2': return '#e67e22'; // Naranja oscuro
        case 'FALLIDA': return '#e74c3c';  // Rojo
        case 'RECHAZADA': return '#9b59b6'; // Morado
        default: return '#3498db';         // Azul
      }
    }

    // Convertir los datos a GeoJSON
    const geoJsonData = convertToGeoJSON(prediosData);

    // Crear capa de predios con estilos dinámicos
    const prediosLayer = L.geoJSON(geoJsonData, {
      style: function(feature) {
        return {
          fillColor: getColorByEstado(feature.properties.ESTADO_VISITA),
          weight: 1,
          opacity: 1,
          color: 'white',
          fillOpacity: 0.7
        };
      },
      onEachFeature: function(feature, layer) {
        const props = feature.properties;
        const popupContent = `
          <b>Subred:</b> ${props.SUBRED}<br>
          <b>Zona:</b> ${props.ZONA}<br>
          <b>Dirección:</b> ${props.DIRECCION}<br>
          <b>Localidad:</b> ${props.LOCALIDAD}<br>
          <b>UPZ:</b> ${props.UPZ}<br>
          <b>Barrio:</b> ${props.COD_BARRIO}<br>
          <b>Territorio:</b> ${props.TERRITORIO}<br>
          <b>Sector Catastral:</b> ${props.SECTOR_CATASTRAL_}<br>
          <b>Manzana:</b> ${props.N_MANZANA}<br>
          <b>Predio:</b> ${props.N_PREDIO}<br>
          <b>Unidad Habitacional:</b> ${props.UNIDAD_HABITACIONAL}<br>
          <b>Estrato:</b> ${props.ESTRATO}<br>
          <b>Estado:</b> ${props.ESTADO_VISITA}<br>
          <b>Motivo:</b> ${props.MOTIVO_ESTADO || 'N/A'}<br>
          <b>Fecha Caracterizado:</b> ${new Date(props.FECHA_CARACTERIZADO).toLocaleDateString()}<br>
          <b>Gestor:</b> ${props.NOMBRE_GESTOR}
        `;
        layer.bindPopup(popupContent);
      }
    }).addTo(map);

    // Añadir control de búsqueda
    const searchControl = new L.Control.Search({
      layer: prediosLayer,
      propertyName: "DIRECCION",
       maxZoom: 22, 
        minZoom: 15,   // Zoom mínimo permitido
      marker: false,
      textPlaceholder: "Buscar dirección...",
      moveToLocation: function(latlng, title, map) {
        map.setView(latlng, 30);
      }
    });

    searchControl.on('search:locationfound', function(e) {
      e.layer.setStyle({ weight: 3, color: '#000', fillOpacity: 0.9 });
      e.layer.openPopup();
    }).on('search:collapsed', function() {
      prediosLayer.resetStyle();
    });

    map.addControl(searchControl);

    // Añadir leyenda
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function(map) {
      const div = L.DomUtil.create('div', 'legend');
      const estados = ['EFECTIVA', 'AUSENTE1', 'AUSENTE2', 'FALLIDA', 'RECHAZADA'];
      const nombres = ['Efectiva', 'Ausente (1)', 'Ausente (2)', 'Fallida', 'Rechazada'];
      
      div.innerHTML = '<h4>Estado de Visita</h4>';
      for (let i = 0; i < estados.length; i++) {
        div.innerHTML += 
          `<i style="background:${getColorByEstado(estados[i])}"></i>${nombres[i]}<br>`;
      }
      return div;
    };
    legend.addTo(map);

    // Ajustar el mapa para mostrar todos los predios
    map.fitBounds(prediosLayer.getBounds(), { padding: [50, 50] });
  </script>
</body>
</html>


