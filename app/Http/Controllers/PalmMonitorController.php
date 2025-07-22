<?php

namespace App\Http\Controllers;

use App\Models\PalmPolygon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PalmMonitorController extends Controller
{
    public function index()
    {
        return view('palm-monitor.index');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license' => 'nullable|string',
            'smallholder' => 'nullable|string',
            'state' => 'nullable|string',
            'district' => 'nullable|string',
            'subdistrict' => 'nullable|string',
            'spoc_name' => 'nullable|string',
            'spoc_code' => 'nullable|string',
            'lot_no' => 'nullable|string',
            'certified_area' => 'nullable|numeric',
            'planted_area' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'mspo' => 'nullable|string',
            'land_title' => 'nullable|string',
            'shape_length' => 'nullable|numeric',
            'shape_area' => 'nullable|numeric',
            'geometry' => 'required|array',
        ]);

        $polygon = PalmPolygon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Polygon saved successfully',
            'data' => $polygon
        ]);
    }

    public function exportGeoJson(): JsonResponse
    {
        $polygons = PalmPolygon::all();

        $features = $polygons->map(function ($polygon) {
            return [
                'type' => 'Feature',
                'properties' => $polygon->only([
                    'license', 'smallholder', 'state', 'district', 'subdistrict',
                    'spoc_name', 'spoc_code', 'lot_no', 'certified_area', 'planted_area',
                    'latitude', 'longitude', 'mspo', 'land_title', 'shape_length', 'shape_area'
                ]),
                'geometry' => $polygon->geometry
            ];
        });

        $geoJson = [
            'type' => 'FeatureCollection',
            'features' => $features
        ];

        return response()->json($geoJson);
    }

    public function exportCsv()
    {
        $polygons = PalmPolygon::all();

        $filename = 'palm_polygons_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($polygons) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'License', 'Smallholder', 'State', 'District', 'Subdistrict',
                'SPOC Name', 'SPOC Code', 'Lot No', 'Certified Area', 'Planted Area',
                'Latitude', 'Longitude', 'MSPO', 'Land Title', 'Shape Length', 'Shape Area'
            ]);

            foreach ($polygons as $polygon) {
                fputcsv($file, [
                    $polygon->license,
                    $polygon->smallholder,
                    $polygon->state,
                    $polygon->district,
                    $polygon->subdistrict,
                    $polygon->spoc_name,
                    $polygon->spoc_code,
                    $polygon->lot_no,
                    $polygon->certified_area,
                    $polygon->planted_area,
                    $polygon->latitude,
                    $polygon->longitude,
                    $polygon->mspo,
                    $polygon->land_title,
                    $polygon->shape_length,
                    $polygon->shape_area,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function loadPolygons(): JsonResponse
    {
        $polygons = PalmPolygon::all();
        return response()->json($polygons);
    }

// Add to controller for GeoJSON import
public function importGeoJson(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:json,geojson'
    ]);

    $contents = file_get_contents($request->file('file')->path());
    $geojson = json_decode($contents, true);

    if ($geojson['type'] === 'FeatureCollection') {
        foreach ($geojson['features'] as $feature) {
            PalmPolygon::create([
                'geometry' => $feature['geometry'],
                // Map other properties
            ]);
        }
    }

    return response()->json(['success' => true]);
}


}
