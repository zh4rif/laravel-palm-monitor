<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSPO Deforestation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <link rel="stylesheet" href="style2.css">
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: transparent;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            background: #d9e1d3;
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            position: relative;

        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #FFD200;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            justify-content: flex-start; /* aligns everything to the left */
            display: flex;
            align-items: left;
            gap: 5px;

        }

        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            color: #ffffff;
        }

        .control-group label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .search-box, .layer-select {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
            min-width: 150px;
        }

        .search-box:focus, .layer-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-btn {
            background: linear-gradient(45deg, #a2ff21, #ffd921);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .search-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .map-container {
            position: relative;
            height: calc(100vh - 80px);
            overflow: hidden;
            background: transparent !important;
        }

        .map-panel {
            position: absolute;
            top: 0;
            bottom: 0;
            overflow: hidden;
            width: 100%;
            transition: clip-path 0.3s ease;
        }

        .left-panel {
            left: 0;
            z-index: 1;
            clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);

        }

        .right-panel {
            right: 0;
            z-index: 2;
            clip-path: polygon(50% 0, 100% 0, 100% 100%, 50% 100%);
        }

        .map {
            width: 100%;
            height: 100%;
        }

        .panel-label {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            color: #333;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .slider-container {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.6));
            z-index: 1002;
            cursor: ew-resize;
            transition: all 0.3s ease;
            left: 50%;
            transform: translateX(-50%);
        }

        .slider-container:hover {
            width: 6px;
            background: linear-gradient(to bottom, rgba(102, 126, 234, 0.8), rgba(102, 126, 234, 0.6));
        }

        .slider-container.dragging {
            width: 6px;
            background: linear-gradient(to bottom, rgba(102, 126, 234, 0.9), rgba(102, 126, 234, 0.7));
        }

        .slider-handle {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 36px;
            height: 36px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: 2px solid white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: ew-resize;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            color: white;
        }

        .slider-handle:hover {
            transform: translate(-50%, -50%) scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .slider-handle.dragging {
            transform: translate(-50%, -50%) scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .slider-handle::before {
            content: '⟷';
            font-size: 14px;
            font-weight: bold;
        }

        .coordinates {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .sync-toggle {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid #e0e0e0;
            padding: 12px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 1001;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .sync-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }

        .sync-toggle.active {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-color: #667eea;
        }

        .loading-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            z-index: 2000;
            display: none;
        }

        .error-message {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #ff4444;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 3000;
            display: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                padding: 10px 15px;
            }

            .controls {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }

            .control-group {
                width: 100%;
            }

            .search-box, .layer-select {
                width: 100%;
                min-width: unset;
            }

            .slider-container {
                width: 6px;
            }

            .slider-handle {
                width: 32px;
                height: 32px;
            }

            .map-container {
                height: calc(100vh - 140px);
            }
        }
    .leaflet-container {
      background: transparent !important; /* Makes Leaflet map background transparent */
    }
.leaflet-draw-toolbar {
    margin-top: 10px;
}


    </style>
</head>
<body>
    <div class="header">
         <div class="logo"></div>
        <img src="https://images.squarespace-cdn.com/content/v1/604db3a6dad32a12b2415387/1636475927545-PK57PVLIB7AEKX1AJQJ8/Logo_MSPO_2020.png" alt="MSPO Logo" style="height: 60px;">
        <div class="logo">MSPO Deforestation Monitoring</div>
         <div class="logo"></div>
        <div class="controls">
            <div class="control-group">
                <label>Search Location</label>
                <input type="text" class="search-box" id="searchBox" placeholder="Enter city or address...">
            </div>
            <div class="control-group">
                <label>Left Panel</label>
                <select class="layer-select" id="leftLayer">
                    <option value="osm">OpenStreetMap</option>
                    <option value="satellite">Satellite</option>
                    <option value="terrain">Terrain</option>
                    <option value="sentinel2020">Sentinel-2 2020</option>
                    <option value="sentinel2021">Sentinel-2 2021</option>
                    <option value="sentinel2022">Sentinel-2 2022</option>
                    <option value="satelogic">Satelogic</option>
                    <option value="forest">Forest Cover</option>
                </select>
            </div>
            <div class="control-group">
                <label>Right Panel</label>
                <select class="layer-select" id="rightLayer">
                    <option value="satellite" selected>Satellite</option>
                    <option value="osm">OpenStreetMap</option>
                    <option value="terrain">Terrain</option>
                    <option value="sentinel2020">Sentinel-2 2020</option>
                    <option value="sentinel2021">Sentinel-2 2021</option>
                    <option value="sentinel2022">Sentinel-2 2022</option>
                    <option value="satelogic">Satelogic</option>
                    <option value="forest">Forest Cover</option>

                </select>
            </div>
            <input type="file" class="file-input" id="importFile" accept=".geojson,.json,.kml,.gpx" style="display: none;">
            <button class="search-btn" id="searchBtn">Search</button>
        </div>
    </div>

    <div class="map-container">
        <div class="map-panel left-panel" id="leftPanel">
            <div class="panel-label">OpenStreetMap</div>
            <div class="map" id="leftMap"></div>
            <div class="coordinates" id="leftCoords">Lat: 0.0000, Lng: 0.0000</div>
        </div>

        <div class="map-panel right-panel" id="rightPanel">
            <div class="panel-label">Satellite</div>
            <div class="map" id="rightMap"></div>
            <div class="coordinates" id="rightCoords">Lat: 0.0000, Lng: 0.0000</div>
        </div>

        <div class="slider-container" id="sliderContainer">
            <div class="slider-handle" id="sliderHandle"></div>
        </div>

        <button class="sync-toggle active" id="syncToggle" title="Toggle map synchronization">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
            </svg>
        </button>
    </div>

    <div class="loading-indicator" id="loadingIndicator">
        <div>Loading...</div>
    </div>

    <div class="error-message" id="errorMessage">
        <div id="errorText"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        // Configuration
        const DEFAULT_LOCATION = [4.4286, 102.0581];
        const DEFAULT_ZOOM = 10;
        const SEARCH_ZOOM = 12;
        const MIN_SLIDER_POSITION = 15; // 15% from left
        const MAX_SLIDER_POSITION = 85; // 85% from left


        // Map layer definitions
        const tileLayers = {
            osm: {
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                attribution: '© OpenStreetMap contributors',
                name: 'OpenStreetMap'
            },
            satellite: {
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                attribution: 'Tiles © Esri',
                name: 'Satellite'
            },
            terrain: {
                url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
                attribution: '© OpenTopoMap contributors',
                name: 'Terrain'
            },
            sentinel2020: {
                url: 'https://tiles.maps.eox.at/wmts/1.0.0/s2cloudless-2020_3857/default/g/{z}/{y}/{x}.jpg',
                attribution: '© SENTINEL-2 Cloudless 2020, EOX IT Services GmbH',
                name: 'Sentinel-2 2020'
            },
            sentinel2021: {
                url: 'https://tiles.maps.eox.at/wmts/1.0.0/s2cloudless-2021_3857/default/g/{z}/{y}/{x}.jpg',
                attribution: '© SENTINEL-2 Cloudless 2021, EOX IT Services GmbH',
                name: 'Sentinel-2 2021'
            },
            sentinel2022: {
                url: 'https://tiles.maps.eox.at/wmts/1.0.0/s2cloudless-2022_3857/default/g/{z}/{y}/{x}.jpg',
                attribution: '© SENTINEL-2 Cloudless 2022, EOX IT Services GmbH',
                name: 'Sentinel-2 2022'
            },

             satelogic: {
                url: 'https://api.maptiler.com/tiles/01981657-13e9-7a7f-b7fc-52db1638775e/{z}/{x}/{y}.png?key=d4jiHMUiXJWY96nrhlQp',
                attribution: '© OpenStreetMap contributors',
                name: 'Satelogic'
            },
            forest: {
                url: 'http://localhost:8080/data/trnforest/{z}/{x}/{y}.jpg',
                attribution: '© Forest Cover 2020, ',
                transparent: true,

                name: 'Forest Cover 2020'
            }

        };

        // Global variables
        let leftMap, rightMap;
        let leftLayer, rightLayer;
        let isSynced = true;
        let mapSyncLock = false;
        let isSliderActive = false;
        let searchMarkers = [];
        let drawLayers = [];
        let isDrawingEnabled = false;
        let currentPolygon = null;
        let polygonPoints = [];
        let currentDrawingMarkers = [];
        let importedLayers = [];
let importedLayerGroups = { left: null, right: null };
        // UI elements
        const elements = {
            leftPanel: document.getElementById('leftPanel'),
            rightPanel: document.getElementById('rightPanel'),
            sliderContainer: document.getElementById('sliderContainer'),
            sliderHandle: document.getElementById('sliderHandle'),
            mapContainer: document.querySelector('.map-container'),
            searchBox: document.getElementById('searchBox'),
            searchBtn: document.getElementById('searchBtn'),
            leftLayer: document.getElementById('leftLayer'),
            rightLayer: document.getElementById('rightLayer'),
            syncToggle: document.getElementById('syncToggle'),
            leftCoords: document.getElementById('leftCoords'),
            rightCoords: document.getElementById('rightCoords'),
            loadingIndicator: document.getElementById('loadingIndicator'),
            errorMessage: document.getElementById('errorMessage'),
            errorText: document.getElementById('errorText')
        };
        function initializeDrawing() {
         // Add drawing controls to both maps
             addDrawingControls();

         // Set up drawing event listeners
             setupDrawingEvents();
        }
function addDrawingControls() {
    const drawingControls = document.createElement('div');
    drawingControls.className = 'control-group';
    drawingControls.innerHTML = `
        <label>Drawing Tools</label>
        <div style="display: flex; gap: 10px;">
            <button class="draw-btn" id="drawPolygonBtn">Draw Polygon</button>
            <button class="draw-btn" id="clearDrawingBtn">Clear</button>
            <button class="draw-btn" id="exportBtn">Export</button>
        </div>
    `;

    // Insert drawing controls before the search button
    const controls = document.querySelector('.controls');
    controls.insertBefore(drawingControls, controls.lastElementChild);
}

// Set up drawing event listeners
function setupDrawingEvents() {
    const drawPolygonBtn = document.getElementById('drawPolygonBtn');
    const clearDrawingBtn = document.getElementById('clearDrawingBtn');
    const exportBtn = document.getElementById('exportBtn');

    drawPolygonBtn.addEventListener('click', togglePolygonDrawing);
    clearDrawingBtn.addEventListener('click', clearAllDrawings);
    exportBtn.addEventListener('click', exportPolygonData);

    // Add click listeners to both maps for drawing
    leftMap.on('click', onMapClick);
    rightMap.on('click', onMapClick);
}

// Toggle polygon drawing mode
function togglePolygonDrawing() {
    const drawBtn = document.getElementById('drawPolygonBtn');

    if (!isDrawingEnabled) {
        // Start drawing
        isDrawingEnabled = true;
        polygonPoints = [];
        drawBtn.textContent = 'Finish Polygon';
        drawBtn.style.background = 'linear-gradient(45deg, #ff6b6b, #ff8e8e)';

        // Change cursor
        document.querySelector('.map-container').style.cursor = 'crosshair';

        // Show instructions
        showMessage('Click on the map to add points. Click "Finish Polygon" to complete.', 'info');
    } else {
        // Finish drawing
        finishPolygonDrawing();
    }
}


// Handle map clicks during drawing
function onMapClick(e) {
    if (!isDrawingEnabled) return;

    const latlng = e.latlng;
    polygonPoints.push([latlng.lat, latlng.lng]);

    // Add point marker
    const leftMarker = L.circleMarker(latlng, {
        radius: 5,
        color: '#667eea',
        fillColor: '#667eea',
        fillOpacity: 0.8
    }).addTo(leftMap);

    const rightMarker = L.circleMarker(latlng, {
        radius: 5,
        color: '#667eea',
        fillColor: '#667eea',
        fillOpacity: 0.8
    }).addTo(rightMap);

    // Store markers for the current drawing session
    currentDrawingMarkers.push({
        left: leftMarker,
        right: rightMarker
    });

    // Update temporary polygon
    updateTempPolygon();

    // Show point count
    showMessage(`Point ${polygonPoints.length} added. ${polygonPoints.length >= 3 ? 'You can finish the polygon now.' : 'Need at least 3 points.'}`, 'info');
}

// Update temporary polygon display
function updateTempPolygon() {
    // Remove existing temp polygon
    if (currentPolygon) {
        leftMap.removeLayer(currentPolygon.left);
        rightMap.removeLayer(currentPolygon.right);
    }

    // Create new temp polygon if we have enough points
    if (polygonPoints.length >= 2) {
        const tempPoints = [...polygonPoints];

        // Create polyline (not closed polygon yet)
        const polyline = {
            left: L.polyline(tempPoints, {
                color: '#667eea',
                weight: 3,
                opacity: 0.7,
                dashArray: '10, 10'
            }).addTo(leftMap),
            right: L.polyline(tempPoints, {
                color: '#667eea',
                weight: 3,
                opacity: 0.7,
                dashArray: '10, 10'
            }).addTo(rightMap)
        };

        currentPolygon = polyline;
    }
}



// Finish polygon drawing
function finishPolygonDrawing() {
    if (polygonPoints.length < 3) {
        showError('Need at least 3 points to create a polygon');
        return;
    }

    // Remove temporary elements
    if (currentPolygon) {
        leftMap.removeLayer(currentPolygon.left);
        rightMap.removeLayer(currentPolygon.right);
    }

    // Create final polygon
    const polygon = {
        left: L.polygon(polygonPoints, {
            color: '#667eea',
            weight: 3,
            opacity: 0.8,
            fillColor: '#667eea',
            fillOpacity: 0.3
        }).addTo(leftMap),
        right: L.polygon(polygonPoints, {
            color: '#667eea',
            weight: 3,
            opacity: 0.8,
            fillColor: '#667eea',
            fillOpacity: 0.3
        }).addTo(rightMap)
    };

    // Add popup with polygon info
    const area = calculatePolygonArea(polygonPoints);
    const popupContent = `
        <div>
            <b>Polygon Info</b><br>
            Points: ${polygonPoints.length}<br>
            Area: ${area.toFixed(2)} km²<br>
            <button onclick="deletePolygon(${drawLayers.length})">Delete</button>
        </div>
    `;

    polygon.left.bindPopup(popupContent);
    polygon.right.bindPopup(popupContent);

    // Store polygon with its associated markers
    drawLayers.push({
        left: polygon.left,
        right: polygon.right,
        points: [...polygonPoints],
        markers: [...currentDrawingMarkers], // Store the markers with the polygon
        type: 'polygon',
        area: area,
        id: drawLayers.length
    });

    // Reset drawing state
    resetDrawingState();

    showMessage(`Polygon created with ${polygonPoints.length} points and area of ${area.toFixed(2)} km²`, 'success');
}
// Reset drawing state
function resetDrawingState() {
    isDrawingEnabled = false;
    polygonPoints = [];
    currentPolygon = null;

    // Reset button
    const drawBtn = document.getElementById('drawPolygonBtn');
    drawBtn.textContent = 'Draw Polygon';
    drawBtn.style.background = 'linear-gradient(45deg, #a2ff21, #ffd921)';

    // Reset cursor
    document.querySelector('.map-container').style.cursor = '';
}

// Calculate polygon area using Shoelace formula
function calculatePolygonArea(points) {
    if (points.length < 3) return 0;

    const R = 6371; // Earth's radius in km
    let area = 0;

    for (let i = 0; i < points.length; i++) {
        const j = (i + 1) % points.length;
        const lat1 = points[i][0] * Math.PI / 180;
        const lat2 = points[j][0] * Math.PI / 180;
        const lng1 = points[i][1] * Math.PI / 180;
        const lng2 = points[j][1] * Math.PI / 180;

        area += (lng2 - lng1) * (2 + Math.sin(lat1) + Math.sin(lat2));
    }

    area = Math.abs(area) * R * R / 2;
    return area;
}

// Delete specific polygon
 function deletePolygon(id) {
            if (drawLayers[id]) {
                const layer = drawLayers[id];

                // Remove polygon from maps
                if (layer.left && leftMap.hasLayer(layer.left)) {
                    leftMap.removeLayer(layer.left);
                }
                if (layer.right && rightMap.hasLayer(layer.right)) {
                    rightMap.removeLayer(layer.right);
                }

                // FIXED: Remove associated markers properly
                if (layer.markers) {
                    layer.markers.forEach(marker => {
                        if (marker.left && leftMap.hasLayer(marker.left)) {
                            leftMap.removeLayer(marker.left);
                        }
                        if (marker.right && rightMap.hasLayer(marker.right)) {
                            rightMap.removeLayer(marker.right);
                        }
                    });
                }

                drawLayers[id] = null;
                showMessage('Polygon and its markers deleted', 'info');
            }
        }

        // FIXED: Clear all drawings
        function clearAllDrawings() {
            // Clear search markers first
            clearSearchMarkers();
            clearImportedFeatures();
            // Clear all draw layers
            drawLayers.forEach(layer => {
                if (layer) {
                    // Remove polygon
                    if (layer.left && leftMap.hasLayer(layer.left)) {
                        leftMap.removeLayer(layer.left);
                    }
                    if (layer.right && rightMap.hasLayer(layer.right)) {
                        rightMap.removeLayer(layer.right);
                    }

                    // FIXED: Remove associated markers properly
                    if (layer.markers) {
                        layer.markers.forEach(marker => {
                            if (marker.left && leftMap.hasLayer(marker.left)) {
                                leftMap.removeLayer(marker.left);
                            }
                            if (marker.right && rightMap.hasLayer(marker.right)) {
                                rightMap.removeLayer(marker.right);
                            }
                        });
                    }
                }
            });

            // FIXED: Clear any temporary drawing markers
            currentDrawingMarkers.forEach(marker => {
                if (marker.left && leftMap.hasLayer(marker.left)) {
                    leftMap.removeLayer(marker.left);
                }
                if (marker.right && rightMap.hasLayer(marker.right)) {
                    rightMap.removeLayer(marker.right);
                }
            });

            // Clear temporary polygon
            if (currentPolygon) {
                leftMap.removeLayer(currentPolygon.left);
                rightMap.removeLayer(currentPolygon.right);
            }

            // Reset all arrays
            drawLayers = [];
            currentDrawingMarkers = [];
            resetDrawingState();
            showMessage('All drawings, markers, and search results cleared', 'info');
        }


// Export polygon data
function exportPolygonData() {
    const polygons = drawLayers.filter(layer => layer && layer.type === 'polygon');
    exportAllFeatures();
    if (polygons.length === 0) {
        showError('No polygons to export');
        return;
    }

    // Create GeoJSON object
    const geojson = {
        type: "FeatureCollection",
        features: polygons.map((polygon, index) => ({
            type: "Feature",
            properties: {
                id: index,
                area_km2: polygon.area,
                points_count: polygon.points.length,
                created_at: new Date().toISOString()
            },
            geometry: {
                type: "Polygon",
                coordinates: [polygon.points.map(point => [point[1], point[0]])] // [lng, lat] for GeoJSON
            }
        }))
    };

    // Create and download file
    const dataStr = JSON.stringify(geojson, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);

    const link = document.createElement('a');
    link.href = url;
    link.download = `polygons_${new Date().toISOString().split('T')[0]}.geojson`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    URL.revokeObjectURL(url);

    showMessage(`Exported ${polygons.length} polygon(s) to GeoJSON file`, 'success');
}

// Show messages to user
function showMessage(message, type = 'info') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-popup ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 120px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'error' ? '#ff4444' : type === 'success' ? '#4CAF50' : '#2196F3'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 3000;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        max-width: 400px;
        text-align: center;
    `;

    document.body.appendChild(messageDiv);

    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.parentNode.removeChild(messageDiv);
        }
    }, 3000);
}

// Add CSS for drawing buttons
const drawingStyle = document.createElement('style');
drawingStyle.textContent = `
    .draw-btn {
        background: linear-gradient(45deg, #a2ff21, #ffd921);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .draw-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .draw-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
`;
document.head.appendChild(drawingStyle);

        // Utility functions
        function showError(message) {
            elements.errorText.textContent = message;
            elements.errorMessage.style.display = 'block';
            setTimeout(() => {
                elements.errorMessage.style.display = 'none';
            }, 5000);
        }

        function showLoading() {
            elements.loadingIndicator.style.display = 'block';
        }

        function hideLoading() {
            elements.loadingIndicator.style.display = 'none';
        }

        function updatePanelLabels() {
            const leftLabel = document.querySelector('.left-panel .panel-label');
            const rightLabel = document.querySelector('.right-panel .panel-label');

            if (leftLabel) {
                leftLabel.textContent = tileLayers[elements.leftLayer.value].name;
            }
            if (rightLabel) {
                rightLabel.textContent = tileLayers[elements.rightLayer.value].name;
            }
        }

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isDrawingEnabled) {
        resetDrawingState();
        showMessage('Drawing cancelled', 'info');
    }
});
        // Map initialization
        function initializeMaps() {
            try {
                // Initialize left map
                leftMap = L.map('leftMap', {
                    zoomControl: false,
                    attributionControl: true
                }).setView(DEFAULT_LOCATION, DEFAULT_ZOOM);

                // Initialize right map
                rightMap = L.map('rightMap', {
                    zoomControl: false,
                    attributionControl: true
                }).setView(DEFAULT_LOCATION, DEFAULT_ZOOM);

                // Add zoom controls
                L.control.zoom({
                    position: 'topright'
                }).addTo(leftMap);

                L.control.zoom({
                    position: 'topright'
                }).addTo(rightMap);

                // Add initial tile layers
                leftLayer = L.tileLayer(tileLayers.osm.url, {
                    attribution: tileLayers.osm.attribution,
                    maxZoom: 18
                }).addTo(leftMap);

                rightLayer = L.tileLayer(tileLayers.satellite.url, {
                    attribution: tileLayers.satellite.attribution,
                    maxZoom: 18
                }).addTo(rightMap);

                // Set up map event listeners
                setupMapEvents();

                // Initial coordinate update
                updateCoordinates(leftMap, elements.leftCoords);
                updateCoordinates(rightMap, elements.rightCoords);

                console.log('Maps initialized successfully');

            } catch (error) {
                console.error('Error initializing maps:', error);
                showError('Failed to initialize maps. Please refresh the page.');
            }
            initializeDrawing();
        }

        // Map event setup
               function setupMapEvents() {
            // Left map events
            leftMap.on('movestart', () => {
                if (leftMap.markerGroup) {
                    leftMap.markerGroup.eachLayer(layer => {
                        if (layer.closePopup) layer.closePopup();
                    });
                }
            });

            leftMap.on('moveend', () => {
                updateCoordinates(leftMap, elements.leftCoords);
                if (isSynced && !mapSyncLock && !isSliderActive) {
                    syncMaps(leftMap, rightMap);
                }
            });

            leftMap.on('zoomend', () => {
                // Force marker re-render after zoom
                if (leftMap.markerGroup) {
                    leftMap.markerGroup.eachLayer(layer => {
                        if (layer._icon) {
                            layer._icon.style.transform = layer._icon.style.transform;
                        }
                    });
                }
            });

            // Right map events
            rightMap.on('movestart', () => {
                if (rightMap.markerGroup) {
                    rightMap.markerGroup.eachLayer(layer => {
                        if (layer.closePopup) layer.closePopup();
                    });
                }
            });

            rightMap.on('moveend', () => {
                updateCoordinates(rightMap, elements.rightCoords);
                if (isSynced && !mapSyncLock && !isSliderActive) {
                    syncMaps(rightMap, leftMap);
                }
            });

            rightMap.on('zoomend', () => {
                // Force marker re-render after zoom
                if (rightMap.markerGroup) {
                    rightMap.markerGroup.eachLayer(layer => {
                        if (layer._icon) {
                            layer._icon.style.transform = layer._icon.style.transform;
                        }
                    });
                }
            });

            // Handle map loading errors
            leftMap.on('tileerror', (e) => {
                console.warn('Left map tile error:', e);
            });

            rightMap.on('tileerror', (e) => {
                console.warn('Right map tile error:', e);
            });
        }

        // Coordinates update
        function updateCoordinates(map, coordsElement) {
            if (!map || !coordsElement) return;

            const center = map.getCenter();
            const zoom = map.getZoom();
            coordsElement.textContent = `Lat: ${center.lat.toFixed(4)}, Lng: ${center.lng.toFixed(4)}, Zoom: ${zoom}`;
        }

        // Map synchronization
        function syncMaps(sourceMap, targetMap) {
            if (!isSynced || !sourceMap || !targetMap || mapSyncLock) return;

            mapSyncLock = true;

            try {
                const center = sourceMap.getCenter();
                const zoom = sourceMap.getZoom();

                // Use setView with animation disabled for better performance
                targetMap.setView(center, zoom, { animate: false });

                // Force marker update after view change
                setTimeout(() => {
                    if (targetMap.markerGroup) {
                        targetMap.markerGroup.eachLayer(layer => {
                            if (layer.update) {
                                layer.update();
                            }
                        });
                    }
                }, 50);

            } catch (error) {
                console.error('Error syncing maps:', error);
            }

            setTimeout(() => {
                mapSyncLock = false;
            }, 100);
        }

        // Slider functionality - Now with overlapping panels
        let isSliderDragging = false;
        let startMouseX = 0;
        let startSliderPosition = 50;

        function updateSliderPosition(positionPercentage) {
            // Clamp between MIN_SLIDER_POSITION and MAX_SLIDER_POSITION
            positionPercentage = Math.max(MIN_SLIDER_POSITION, Math.min(MAX_SLIDER_POSITION, positionPercentage));

            // Move the slider
            elements.sliderContainer.style.left = `${positionPercentage}%`;

            // Update clip-path for overlapping effect
            elements.leftPanel.style.clipPath = `polygon(0 0, ${positionPercentage}% 0, ${positionPercentage}% 100%, 0 100%)`;
            elements.rightPanel.style.clipPath = `polygon(${positionPercentage}% 0, 100% 0, 100% 100%, ${positionPercentage}% 100%)`;
        }

        function getMouseX(e) {
            return e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
        }

        function startSliderDrag(e) {
            e.preventDefault();
            e.stopPropagation();

            isSliderDragging = true;
            isSliderActive = true;
            startMouseX = getMouseX(e);

            // Get current slider position as percentage
            const containerRect = elements.mapContainer.getBoundingClientRect();
            const sliderRect = elements.sliderContainer.getBoundingClientRect();
            startSliderPosition = ((sliderRect.left - containerRect.left) / containerRect.width) * 100;

            // Add visual feedback
            elements.sliderContainer.classList.add('dragging');
            elements.sliderHandle.classList.add('dragging');

            // Add global event listeners
            document.addEventListener('mousemove', onSliderDrag, { passive: false });
            document.addEventListener('mouseup', stopSliderDrag);
            document.addEventListener('touchmove', onSliderDrag, { passive: false });
            document.addEventListener('touchend', stopSliderDrag);

            // Prevent text selection and set cursor
            document.body.style.cursor = 'ew-resize';
            document.body.style.userSelect = 'none';
        }

        function onSliderDrag(e) {
            if (!isSliderDragging) return;

            e.preventDefault();
            e.stopPropagation();

            const currentMouseX = getMouseX(e);
            const deltaX = currentMouseX - startMouseX;
            const containerWidth = elements.mapContainer.offsetWidth;
            const deltaPercentage = (deltaX / containerWidth) * 100;

            const newPosition = startSliderPosition + deltaPercentage;
            updateSliderPosition(newPosition);
        }

        function stopSliderDrag(e) {
            if (!isSliderDragging) return;

            e.preventDefault();
            e.stopPropagation();

            isSliderDragging = false;

            // Remove visual feedback
            elements.sliderContainer.classList.remove('dragging');
            elements.sliderHandle.classList.remove('dragging');

            // Remove global event listeners
            document.removeEventListener('mousemove', onSliderDrag);
            document.removeEventListener('mouseup', stopSliderDrag);
            document.removeEventListener('touchmove', onSliderDrag);
            document.removeEventListener('touchend', stopSliderDrag);

            // Reset cursor and selection
            document.body.style.cursor = '';
            document.body.style.userSelect = '';

            // Reset slider active state after a short delay
            setTimeout(() => {
                isSliderActive = false;
            }, 300);
        }

        // Layer switching
        function switchLayer(map, currentLayer, layerType) {
            if (!map || !currentLayer) return null;

            try {
                map.removeLayer(currentLayer);
                const selectedLayer = tileLayers[layerType];
                const newLayer = L.tileLayer(selectedLayer.url, {
                    attribution: selectedLayer.attribution,
                    maxZoom: 18
                }).addTo(map);

                return newLayer;
            } catch (error) {
                console.error('Error switching layer:', error);
                showError('Failed to switch map layer');
                return currentLayer;
            }
        }

        // Search functionality
function clearSearchMarkers() {
            console.log('Clearing search markers...');

            // Method 1: Use layer groups if available
            if (leftMap && leftMap.markerGroup) {
                leftMap.markerGroup.clearLayers();
            }
            if (rightMap && rightMap.markerGroup) {
                rightMap.markerGroup.clearLayers();
            }

            // Method 2: Manual cleanup of stored markers
            searchMarkers.forEach(markerPair => {
                try {
                    if (markerPair.left && leftMap && leftMap.hasLayer(markerPair.left)) {
                        leftMap.removeLayer(markerPair.left);
                    }
                    if (markerPair.right && rightMap && rightMap.hasLayer(markerPair.right)) {
                        rightMap.removeLayer(markerPair.right);
                    }
                } catch (error) {
                    console.warn('Error removing search marker:', error);
                }
            });

            // Clear the array
            searchMarkers = [];
            console.log('Search markers cleared');
        }

        // FIXED: Enhanced search function with better marker handling
        async function searchLocation(query) {
            if (!query || query.trim() === '') {
                showError('Please enter a search term');
                return false;
            }

            showLoading();
            elements.searchBtn.disabled = true;

            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&addressdetails=1`,
                    {
                        headers: {
                            'User-Agent': 'MSPO Deforestation Maps'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('Search service unavailable');
                }

                const data = await response.json();

                if (data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);

                    if (isNaN(lat) || isNaN(lng)) {
                        throw new Error('Invalid coordinates received');
                    }

                    // Clear existing markers before creating new ones
                    clearSearchMarkers();

                    // Wait for cleanup to complete
                    await new Promise(resolve => setTimeout(resolve, 100));

                    // Verify maps are still available
                    if (!leftMap || !rightMap) {
                        throw new Error('Maps not available');
                    }

                    // Create custom icon for search results
                    const searchIcon = L.icon({
                        iconUrl: 'data:image/svg+xml;base64,' + btoa(`
                            <svg width="25" height="41" viewBox="0 0 25 41" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 0C5.596 0 0 5.596 0 12.5c0 12.5 12.5 28.5 12.5 28.5s12.5-16 12.5-28.5C25 5.596 19.404 0 12.5 0z" fill="#FF6B6B"/>
                                <circle cx="12.5" cy="12.5" r="6" fill="white"/>
                            </svg>
                        `),
                        iconSize: [25, 41],
                        iconAnchor: [12.5, 41],
                        popupAnchor: [0, -35]
                    });

                    // Create new markers with improved positioning
                    const markerLeft = L.marker([lat, lng], {
                        icon: searchIcon,
                        title: data[0].display_name,
                        riseOnHover: true,
                        zIndexOffset: 1000
                    });

                    const markerRight = L.marker([lat, lng], {
                        icon: searchIcon,
                        title: data[0].display_name,
                        riseOnHover: true,
                        zIndexOffset: 1000
                    });

                    // Add to layer groups if available, otherwise add directly
                    if (leftMap.markerGroup) {
                        leftMap.markerGroup.addLayer(markerLeft);
                    } else {
                        markerLeft.addTo(leftMap);
                    }

                    if (rightMap.markerGroup) {
                        rightMap.markerGroup.addLayer(markerRight);
                    } else {
                        markerRight.addTo(rightMap);
                    }

                    // Add popups
                    const popupContent = `
                        <div style="max-width: 200px;">
                            <b>${data[0].display_name}</b><br>
                            <small>Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}</small>
                        </div>
                    `;

                    markerLeft.bindPopup(popupContent).openPopup();
                    markerRight.bindPopup(popupContent).openPopup();

                    // Store markers for cleanup
                    searchMarkers.push({
                        left: markerLeft,
                        right: markerRight,
                        location: data[0].display_name,
                        coordinates: [lat, lng]
                    });

                    // FIXED: Improved map view synchronization
                    const moveToLocation = () => {
                        if (leftMap && rightMap) {
                            leftMap.setView([lat, lng], SEARCH_ZOOM);
                            if (isSynced) {
                                rightMap.setView([lat, lng], SEARCH_ZOOM);
                            }
                        }
                    };

                    // Use requestAnimationFrame for smoother animation
                    requestAnimationFrame(moveToLocation);

                    console.log(`Search successful: ${data[0].display_name}`);
                    showMessage(`Found: ${data[0].display_name}`, 'success');
                    return true;
                } else {
                    showError('Location not found. Please try a different search term.');
                    return false;
                }
            } catch (error) {
                console.error('Search error:', error);
                showError('Search failed. Please try again.');
                return false;
            } finally {
                hideLoading();
                elements.searchBtn.disabled = false;
            }
        }


// Additional cleanup function for when maps are destroyed or reset
function cleanupAllMarkers() {
    // Clear search markers
    clearSearchMarkers();

    // Clear drawing markers
    currentDrawingMarkers.forEach(markerPair => {
        try {
            if (markerPair.left && leftMap && leftMap.hasLayer(markerPair.left)) {
                leftMap.removeLayer(markerPair.left);
            }
            if (markerPair.right && rightMap && rightMap.hasLayer(markerPair.right)) {
                rightMap.removeLayer(markerPair.right);
            }
        } catch (error) {
            console.warn('Error removing drawing marker:', error);
        }
    });

    currentDrawingMarkers = [];

    console.log('All markers cleaned up');
}

// Enhanced window beforeunload cleanup
window.addEventListener('beforeunload', () => {
    cleanupAllMarkers();
});

// Add this to the existing clearAllDrawings function to also clear search markers
function clearAllDrawings() {
    // Clear search markers first
    clearSearchMarkers();

    // ... rest of existing clearAllDrawings code ...

    drawLayers.forEach(layer => {
        if (layer) {
            // Remove polygon
            if (layer.left && leftMap.hasLayer(layer.left)) {
                leftMap.removeLayer(layer.left);
            }
            if (layer.right && rightMap.hasLayer(layer.right)) {
                rightMap.removeLayer(layer.right);
            }

            // Remove associated markers
            if (layer.markers) {
                layer.markers.forEach(marker => {
                    if (marker.left && leftMap.hasLayer(marker.left)) {
                        leftMap.removeLayer(marker.left);
                    }
                    if (marker.right && rightMap.hasLayer(marker.right)) {
                        rightMap.removeLayer(marker.right);
                    }
                });
            }
        }
    });

    // Clear any temporary drawing markers
    currentDrawingMarkers.forEach(marker => {
        if (marker.left && leftMap.hasLayer(marker.left)) {
            leftMap.removeLayer(marker.left);
        }
        if (marker.right && rightMap.hasLayer(marker.right)) {
            rightMap.removeLayer(marker.right);
        }
    });

    // Clear temporary polygon
    if (currentPolygon) {
        leftMap.removeLayer(currentPolygon.left);
        rightMap.removeLayer(currentPolygon.right);
    }

    drawLayers = [];
    currentDrawingMarkers = [];
    resetDrawingState();
    showMessage('All drawings, markers, and search results cleared', 'info');
}
        // Event listeners setup
        function setupEventListeners() {
            // Slider events
            elements.sliderContainer.addEventListener('mousedown', startSliderDrag);
            elements.sliderHandle.addEventListener('mousedown', startSliderDrag);
            elements.sliderContainer.addEventListener('touchstart', startSliderDrag);
            elements.sliderHandle.addEventListener('touchstart', startSliderDrag);

            // Prevent context menu on slider
            elements.sliderContainer.addEventListener('contextmenu', (e) => e.preventDefault());
            elements.sliderHandle.addEventListener('contextmenu', (e) => e.preventDefault());

            // Sync toggle
            elements.syncToggle.addEventListener('click', () => {
                isSynced = !isSynced;
                elements.syncToggle.classList.toggle('active', isSynced);

                if (isSynced && leftMap && rightMap) {
                    syncMaps(leftMap, rightMap);
                }
            });

            // Layer switching
            elements.leftLayer.addEventListener('change', (e) => {
                if (leftMap && leftLayer) {
                    leftLayer = switchLayer(leftMap, leftLayer, e.target.value);
                    updatePanelLabels();
                }
            });

            elements.rightLayer.addEventListener('change', (e) => {
                if (rightMap && rightLayer) {
                    rightLayer = switchLayer(rightMap, rightLayer, e.target.value);
                    updatePanelLabels();
                }
            });

            // Search functionality
            elements.searchBtn.addEventListener('click', async () => {
                const query = elements.searchBox.value.trim();
                await searchLocation(query);
            });

            elements.searchBox.addEventListener('keypress', async (e) => {
                if (e.key === 'Enter') {
                    const query = e.target.value.trim();
                    await searchLocation(query);
                }
            });

            // Window resize
            window.addEventListener('resize', () => {
                setTimeout(() => {
                    if (leftMap) leftMap.invalidateSize();
                    if (rightMap) rightMap.invalidateSize();
                }, 300);
            });

            // Error message click to dismiss
            elements.errorMessage.addEventListener('click', () => {
                elements.errorMessage.style.display = 'none';
            });
        }

        // Initialize everything when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM loaded, initializing application...');

            // Set up event listeners first
            setupEventListeners();

            // Initialize maps after a short delay
            setTimeout(() => {
                initializeMaps();
                initializeImportFeature();
                updatePanelLabels();
            }, 100);
        });

        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                // Page became visible, invalidate map sizes
                setTimeout(() => {
                    if (leftMap) leftMap.invalidateSize();
                    if (rightMap) rightMap.invalidateSize();
                }, 100);
            }
        });

        function initializeImportedLayerGroups() {
    if (!importedLayerGroups.left) {
        importedLayerGroups.left = L.layerGroup().addTo(leftMap);
    }
    if (!importedLayerGroups.right) {
        importedLayerGroups.right = L.layerGroup().addTo(rightMap);
    }
}

// Add import button to the UI (add this to your existing controls)
function addImportButton() {
    const importControl = document.createElement('div');
    importControl.className = 'control-group';
    importControl.innerHTML = `
        <label>Import Data</label>
        <button class="import-btn" id="importBtn">Import GeoJSON</button>
        <input type="file" id="importFileInput" accept=".geojson,.json,.kml,.gpx" style="display: none;">
    `;

    // Insert before the search button
    const controls = document.querySelector('.controls');
    controls.insertBefore(importControl, controls.lastElementChild);

    // Add event listeners
    const importBtn = document.getElementById('importBtn');
    const importFileInput = document.getElementById('importFileInput');

    importBtn.addEventListener('click', () => {
        importFileInput.click();
    });

    importFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (['geojson', 'json'].includes(fileExtension)) {
                importGeoJSON(file);
            } else {
                showError('Please select a GeoJSON file (.geojson or .json)');
            }

            // Clear the input
            this.value = '';
        }
    });
}


// Trigger file import dialog
function triggerFileImport() {
    elements.importFile.click();
}

// Main import function
function importGeoJSON(file) {
    const reader = new FileReader();

    reader.onload = function(e) {
        try {
            const geojsonData = JSON.parse(e.target.result);
            processGeoJSONData(geojsonData);
        } catch (error) {
            console.error('Error parsing GeoJSON:', error);
            showError('Invalid GeoJSON file format');
        }
    };

    reader.onerror = function() {
        showError('Error reading file');
    };

    reader.readAsText(file);
}

// Process and display GeoJSON data
function processGeoJSONData(geojsonData) {
    try {
        // Initialize layer groups if not already done
        initializeImportedLayerGroups();

        // Create styling function
        const getFeatureStyle = (feature) => {
            const baseStyle = {
                color: '#ff6b6b',
                weight: 2,
                opacity: 0.8,
                fillColor: '#ff6b6b',
                fillOpacity: 0.3
            };

            // Override with feature properties if available
            if (feature.properties && feature.properties.style) {
                return { ...baseStyle, ...feature.properties.style };
            }

            return baseStyle;
        };

        // Create popup content function
        const getPopupContent = (feature) => {
            let content = '<div><b>Imported Feature</b><br>';

            if (feature.properties) {
                Object.entries(feature.properties).forEach(([key, value]) => {
                    if (key !== 'style') {
                        content += `<b>${key}:</b> ${value}<br>`;
                    }
                });
            }

            // Add geometry info
            if (feature.geometry) {
                content += `<b>Type:</b> ${feature.geometry.type}<br>`;

                if (feature.geometry.type === 'Polygon') {
                    const coords = feature.geometry.coordinates[0];
                    const area = calculatePolygonAreaFromCoords(coords);
                    content += `<b>Area:</b> ${area.toFixed(2)} km²<br>`;
                }
            }

            content += `<button onclick="removeImportedFeature('${feature.id || Math.random()}')">Remove</button>`;
            content += '</div>';

            return content;
        };

        // Process each feature
        let featureCount = 0;
        let bounds = L.latLngBounds();

        const processFeature = (feature, layer) => {
            featureCount++;

            // Set style
            const style = getFeatureStyle(feature);
            layer.setStyle(style);

            // Add popup
            const popupContent = getPopupContent(feature);
            layer.bindPopup(popupContent);

            // Extend bounds
            if (layer.getBounds) {
                bounds.extend(layer.getBounds());
            } else if (layer.getLatLng) {
                bounds.extend(layer.getLatLng());
            }

            // Store reference
            const layerData = {
                id: feature.id || Math.random(),
                feature: feature,
                leftLayer: layer,
                rightLayer: null,
                type: feature.geometry.type
            };

            return layerData;
        };

        // Create left map layer
        const leftGeoJSONLayer = L.geoJSON(geojsonData, {
            onEachFeature: function(feature, layer) {
                const layerData = processFeature(feature, layer);
                layerData.leftLayer = layer;

                // Add to imported layers array
                importedLayers.push(layerData);
            }
        });

        // Create right map layer (clone)
        const rightGeoJSONLayer = L.geoJSON(geojsonData, {
            onEachFeature: function(feature, layer) {
                const style = getFeatureStyle(feature);
                layer.setStyle(style);

                const popupContent = getPopupContent(feature);
                layer.bindPopup(popupContent);

                // Find corresponding layer data and update right layer
                const layerData = importedLayers.find(l =>
                    l.feature.id === feature.id ||
                    JSON.stringify(l.feature.geometry) === JSON.stringify(feature.geometry)
                );

                if (layerData) {
                    layerData.rightLayer = layer;
                }
            }
        });

        // Add to layer groups
        importedLayerGroups.left.addLayer(leftGeoJSONLayer);
        importedLayerGroups.right.addLayer(rightGeoJSONLayer);

        // Fit map to imported data if bounds are valid
        if (bounds.isValid()) {
            leftMap.fitBounds(bounds, { padding: [20, 20] });
            if (isSynced) {
                rightMap.fitBounds(bounds, { padding: [20, 20] });
            }
        }

        showMessage(`Successfully imported ${featureCount} feature(s)`, 'success');

    } catch (error) {
        console.error('Error processing GeoJSON:', error);
        showError('Error processing GeoJSON data');
    }
}

// Calculate polygon area from coordinates
function calculatePolygonAreaFromCoords(coords) {
    if (coords.length < 3) return 0;

    // Convert to [lat, lng] format for existing calculation function
    const points = coords.map(coord => [coord[1], coord[0]]);
    return calculatePolygonArea(points);
}

// Remove specific imported feature
function removeImportedFeature(featureId) {
    const layerIndex = importedLayers.findIndex(layer => layer.id === featureId);

    if (layerIndex !== -1) {
        const layerData = importedLayers[layerIndex];

        // Remove from maps
        if (layerData.leftLayer) {
            importedLayerGroups.left.removeLayer(layerData.leftLayer);
        }
        if (layerData.rightLayer) {
            importedLayerGroups.right.removeLayer(layerData.rightLayer);
        }

        // Remove from array
        importedLayers.splice(layerIndex, 1);

        showMessage('Feature removed', 'info');
    }
}

// Clear all imported features
function clearImportedFeatures() {
    if (importedLayerGroups.left) {
        importedLayerGroups.left.clearLayers();
    }
    if (importedLayerGroups.right) {
        importedLayerGroups.right.clearLayers();
    }

    importedLayers = [];
    showMessage('All imported features cleared', 'info');
}

// Add clear imported button to existing clear function
function updateClearAllDrawings() {
    // Add this to your existing clearAllDrawings function
    clearImportedFeatures();
}

// File input event listener
elements.importFile.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileExtension = file.name.split('.').pop().toLowerCase();

        if (['geojson', 'json'].includes(fileExtension)) {
            importGeoJSON(file);
        } else {
            showError('Please select a GeoJSON file (.geojson or .json)');
        }

        // Clear the input
        this.value = '';
    }
});

// Initialize import functionality (call this in your existing initialization)
function initializeImportFeature() {
    addImportButton();
    initializeImportedLayerGroups();
}

// Export imported features back to GeoJSON
function exportAllFeatures() {
    const allFeatures = [];

    // Add drawn polygons
    const drawnPolygons = drawLayers.filter(layer => layer && layer.type === 'polygon');
    drawnPolygons.forEach(polygon => {
        allFeatures.push({
            type: "Feature",
            properties: {
                type: "drawn_polygon",
                area_km2: polygon.area,
                points_count: polygon.points.length,
                created_at: new Date().toISOString()
            },
            geometry: {
                type: "Polygon",
                coordinates: [polygon.points.map(point => [point[1], point[0]])]
            }
        });
    });

    // Add imported features
    importedLayers.forEach(layer => {
        if (layer.feature) {
            allFeatures.push(layer.feature);
        }
    });

    if (allFeatures.length === 0) {
        showError('No features to export');
        return;
    }

    const geojson = {
        type: "FeatureCollection",
        features: allFeatures
    };

    // Download file
    const dataStr = JSON.stringify(geojson, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);

    const link = document.createElement('a');
    link.href = url;
    link.download = `all_features_${new Date().toISOString().split('T')[0]}.geojson`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    URL.revokeObjectURL(url);

    showMessage(`Exported ${allFeatures.length} feature(s) to GeoJSON file`, 'success');
}

// Add this CSS for import button styling
const importButtonStyle = document.createElement('style');
importButtonStyle.textContent = `
    .import-btn {
        background: linear-gradient(45deg, #4CAF50, #45a049);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .import-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .import-btn:active {
        transform: translateY(0);
    }
`;
document.head.appendChild(importButtonStyle);
    </script>
</body>
</html>
