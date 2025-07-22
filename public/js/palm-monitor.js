// Palm Monitor JavaScript Functions

// Initialize the map
const map = L.map('map').setView([4.4286, 102.0581], 12);

// Define base layers
let baseLayers = {
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Esri, DigitalGlobe, GeoEye, Earthstar Geographics'
    }),
    terrain: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Esri, DeLorme, NAVTEQ'
    }),
    osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'OpenStreetMap contributors'
    })
};

// Add default satellite layer
baseLayers.satellite.addTo(map);

// Initialize coordinate display and drawing controls
const coordDiv = document.getElementById('coordinates');
const drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

const drawControl = new L.Control.Draw({
    edit: { featureGroup: drawnItems },
    draw: {
        polygon: true,
        polyline: true,
        rectangle: true,
        circle: true,
        marker: true,
        circlemarker: true
    }
});
map.addControl(drawControl);

// Global variables
let currentLayer = null;
let currentCircle = null;
const geojsonFeatures = [];

// Event handler for drawing shapes
map.on(L.Draw.Event.CREATED, function (e) {
    const layer = e.layer;
    const type = e.layerType;

    drawnItems.addLayer(layer);

    if (type === 'circle') {
        currentCircle = layer;
        const center = layer.getLatLng();
        const radius = layer.getRadius();

        layer.bindPopup(`
            <strong>New Circle</strong><br>
            Center: ${center.lat.toFixed(6)}, ${center.lng.toFixed(6)}<br>
            Radius: ${(radius/1000).toFixed(2)} km<br>
            <button onclick="analyzePolygon()" class="btn" style="background: #3498db; color: white;">Analyze</button>
            <button onclick="deleteShape()" class="btn" style="background: #e74c3c; color: white;">Delete</button>
        `);
    } else if (type === 'marker') {
        const latlng = layer.getLatLng();
        layer.bindPopup(`
            <strong>Marker</strong><br>
            Location: ${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}<br>
            <button onclick="deleteShape()" class="btn" style="background: #e74c3c; color: white;">Delete</button>
        `);
    } else {
        currentLayer = layer;
        const center = layer.getBounds().getCenter();
        document.getElementById('latitude').value = center.lat.toFixed(10);
        document.getElementById('longitude').value = center.lng.toFixed(10);

        const geojson = layer.toGeoJSON();
        if (geojson.geometry.coordinates && geojson.geometry.coordinates[0]) {
            const coords = geojson.geometry.coordinates[0];
            document.getElementById('shapeLength').value = (coords.length * 0.0001).toFixed(6);
            document.getElementById('shapeArea').value = (Math.random() * 1e-6).toFixed(12);
        }

        document.getElementById('infoForm').style.display = 'block';
    }
});

// Update coordinates on mouse move
map.on('mousemove', function (e) {
    const { lat, lng } = e.latlng;
    coordDiv.textContent = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
});

// Layer toggle function
function toggleLayer(layerName) {
    // Remove active class from all imagery buttons
    document.querySelectorAll('.tool-group:first-child .tool-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Add active class to clicked button
    event.target.classList.add('active');

    switch(layerName) {
        case 'satellite':
            // Remove other layers
            if (map.hasLayer(baseLayers.terrain)) {
                map.removeLayer(baseLayers.terrain);
            }
            if (map.hasLayer(baseLayers.osm)) {
                map.removeLayer(baseLayers.osm);
            }
            // Add satellite layer
            if (!map.hasLayer(baseLayers.satellite)) {
                baseLayers.satellite.addTo(map);
            }
            break;
        case 'terrain':
            // Remove other layers
            if (map.hasLayer(baseLayers.satellite)) {
                map.removeLayer(baseLayers.satellite);
            }
            if (map.hasLayer(baseLayers.osm)) {
                map.removeLayer(baseLayers.osm);
            }
            // Add terrain layer
            if (!map.hasLayer(baseLayers.terrain)) {
                baseLayers.terrain.addTo(map);
            }
            break;
        case 'ndvi':
            showNotification('NDVI overlay activated');
            break;
    }
}

// Delete shape function
function deleteShape() {
    const popup = map._popup;
    if (popup && popup._source) {
        const layer = popup._source;
        drawnItems.removeLayer(layer);
        map.closePopup();
    }
}

// Analyze polygon function
function analyzePolygon() {
    showNotification('Analysis started for selected area...');
}

// Request new imagery
function requestNewImagery() {
    showNotification('Requesting new imagery update...');
}

// Spatial analysis function
function runSpatialAnalysis() {
    showNotification('Running spatial analysis...');
}

// Generate report function
function generateReport() {
    showNotification('Generating report...');

    const link = document.createElement('a');
    link.href = 'MSPO REPORT_14072025.pdf';
    link.download = 'report.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Trend analysis function
function trendAnalysis() {
    showNotification('Performing trend analysis...');
}

// Export data function
function exportData(format) {
    switch(format) {
        case 'geojson':
            exportGeoJSON();
            break;
        case 'shapefile':
            showNotification('Shapefile export functionality would be implemented here');
            break;
        case 'csv':
            exportCSV();
            break;
        case 'report':
            generateReport();
            break;
    }
}

// Export CSV function
function exportCSV() {
    if (geojsonFeatures.length === 0) {
        showNotification('No data to export');
        return;
    }

    const headers = Object.keys(geojsonFeatures[0].properties);
    const csvContent = [
        headers.join(','),
        ...geojsonFeatures.map(feature =>
            headers.map(header => feature.properties[header] || '').join(',')
        )
    ].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'polygons.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Show notification function
function showNotification(message) {
    alert(message);
}

// Close info form function
function closeInfoForm() {
    document.getElementById('infoForm').style.display = 'none';
    // Clear form
    document.querySelectorAll('#infoForm input').forEach(input => {
        if (!input.readOnly) {
            input.value = '';
        }
    });
    currentLayer = null;
}

// Save info function
function saveInfo() {
    if (!currentLayer) {
        showNotification("Draw a polygon first.");
        return;
    }

    const get = id => document.getElementById(id).value;

    const properties = {
        license: get('license'),
        smallholder: get('name'),
        state: get('state'),
        district: get('district'),
        subdistrict: get('subdistrict'),
        spoc_name: get('spocName'),
        spoc_code: get('spocCode'),
        lot_no: get('lotNo'),
        certified_area: get('certified'),
        planted_area: get('planted'),
        latitude: get('latitude'),
        longitude: get('longitude'),
        mspo: get('mspo'),
        land_title: get('land'),
        shape_length: get('shapeLength'),
        shape_area: get('shapeArea')
    };

    const feature = currentLayer.toGeoJSON();
    feature.properties = properties;
    geojsonFeatures.push(feature);

    const popupContent = Object.entries(properties)
        .filter(([key, val]) => val)
        .map(([key, val]) => `<b>${key.replace(/_/g, ' ').toUpperCase()}:</b> ${val}`)
        .join("<br>");

    currentLayer.bindPopup(popupContent).openPopup();

    closeInfoForm();
    showNotification('Polygon information saved successfully!');
}

// Export GeoJSON function
function exportGeoJSON() {
    if (geojsonFeatures.length === 0) {
        showNotification('No data to export');
        return;
    }

    const geojson = {
        type: "FeatureCollection",
        features: geojsonFeatures
    };
    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(geojson, null, 2));
    const dlAnchor = document.createElement('a');
    dlAnchor.setAttribute("href", dataStr);
    dlAnchor.setAttribute("download", "polygons.geojson");
    document.body.appendChild(dlAnchor);
    dlAnchor.click();
    dlAnchor.remove();
    showNotification('GeoJSON file exported successfully!');
}

// Handle file import function
function handleFileImport(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const geojson = JSON.parse(e.target.result);
                L.geoJSON(geojson, {
                    onEachFeature: function(feature, layer) {
                        drawnItems.addLayer(layer);
                        if (feature.properties) {
                            const popupContent = Object.entries(feature.properties)
                                .filter(([key, val]) => val)
                                .map(([key, val]) => `<b>${key.replace(/_/g, ' ').toUpperCase()}:</b> ${val}`)
                                .join("<br>");
                            layer.bindPopup(popupContent);
                        }
                        // Add to geojsonFeatures array
                        geojsonFeatures.push(feature);
                    }
                });
                showNotification('GeoJSON file imported successfully!');
            } catch (error) {
                showNotification('Error parsing GeoJSON file: ' + error.message);
            }
        };
        reader.readAsText(file);
    }
}
