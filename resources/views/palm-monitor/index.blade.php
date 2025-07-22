<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Palm Monitor - Geospatial Intelligence Platform</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/palm-monitor.css') }}">
    <!-- Alternative paths to try if above doesn't work -->
    <!-- <link rel="stylesheet" href="/css/palm-monitor.css"> -->
    <!-- <link rel="stylesheet" href="{{ url('css/palm-monitor.css') }}"> -->
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="https://images.squarespace-cdn.com/content/v1/604db3a6dad32a12b2415387/1636475927545-PK57PVLIB7AEKX1AJQJ8/Logo_MSPO_2020.png" alt="MSPO Logo" style="height: 60px;">
            <div>
                <span style="font-weight: bold;">MSPO-Palm Monitor</span><br>
                <span style="font-size: 12px; opacity: 0.7;">Powered by Uzma Digital Earth</span>
            </div>
        </div>

        <div class="user-info">
            <div class="status-indicator">
                <div class="status-dot"></div>
                <span>Live Monitoring</span>
            </div>
            <i class="fas fa-user-circle" style="font-size: 24px;"></i>
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <div class="tool-group">
                <h3><i class="fas fa-satellite"></i> Imagery</h3>
                <button class="tool-btn active" onclick="toggleLayer('satellite')">
                    <i class="fas fa-globe"></i> High-Res Satellite
                </button>
                <button class="tool-btn" onclick="toggleLayer('terrain')">
                    <i class="fas fa-mountain"></i> Topographic Data
                </button>
                <button class="tool-btn" onclick="toggleLayer('ndvi')">
                    <i class="fas fa-seedling"></i> NDVI Analysis
                </button>
                <button class="tool-btn" onclick="requestNewImagery()">
                    <i class="fas fa-refresh"></i> Request Update
                </button>
                <button class="tool-btn" onclick="window.location.href='{{ route('deforestation') }}'">
                    Deforestation Map
                </button>
            </div>

            <div class="tool-group">
                <h3><i class="fas fa-chart-line"></i> Analysis</h3>
                <button class="tool-btn" onclick="runSpatialAnalysis()">
                    <i class="fas fa-calculator"></i> Spatial Analysis
                </button>
                <button class="tool-btn" onclick="generateReport()">
                    <i class="fas fa-file-alt"></i> Generate Report
                </button>
                <button class="tool-btn" onclick="trendAnalysis()">
                    <i class="fas fa-trending-up"></i> Trend Analysis
                </button>
            </div>

            <div class="tool-group">
                <h3><i class="fas fa-upload"></i> Import/Export Data</h3>
                <div class="file-input-wrapper">
                    <input type="file" id="fileInput" accept=".geojson,.json" onchange="handleFileImport(event)" />
                    <div class="export-buttons">
                        <button class="export-btn" onclick="exportData('geojson')">
                            <i class="fas fa-download"></i> Export GeoJSON
                        </button>
                        <button class="export-btn" onclick="exportData('shapefile')">
                            <i class="fas fa-download"></i> Export Shapefile
                        </button>
                        <button class="export-btn" onclick="exportData('csv')">
                            <i class="fas fa-download"></i> Export CSV
                        </button>
                        <button class="export-btn" onclick="exportData('report')">
                            <i class="fas fa-file-pdf"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="map-container">
            <div id="coordinates">Lat: -, Lng: -</div>
            <div id="map"></div>

            <div id="infoForm">
                <div class="form-header">
                    <h3>Polygon Info</h3>
                    <button class="close-btn" onclick="closeInfoForm()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="form-grid">
                    <input id="license" placeholder="License no." />
                    <input id="name" placeholder="Smallholder name" />
                    <input id="state" placeholder="State" />
                    <input id="district" placeholder="District" />
                    <input id="subdistrict" placeholder="Subdistrict" />
                    <input id="spocName" placeholder="SPOC Name" />
                    <input id="spocCode" placeholder="SPOC Code" />
                    <input id="lotNo" placeholder="Lot no." />
                    <input id="certified" placeholder="Certified Area (HA)" />
                    <input id="planted" placeholder="Planted Area (HA)" />
                    <input id="latitude" placeholder="Latitude" readonly />
                    <input id="longitude" placeholder="Longitude" readonly />
                    <input id="mspo" placeholder="MSPO Certification" />
                    <input id="land" placeholder="Land Title" />
                    <input id="shapeLength" placeholder="Shape_Length" />
                    <input id="shapeArea" placeholder="Shape_Area" />
                </div>
                <div class="form-actions">
                    <button onclick="saveInfo()">Save Info</button>
                    <button onclick="exportGeoJSON()">Download GeoJSON</button>
                </div>
            </div>
        </div>
    </div>

    <!-- External JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('js/palm-monitor.js') }}"></script>
</body>
</html>
