<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PalmMonitorController;
Route::get('/deforestation', function () {
    return view('deforestation');
})->name('deforestation');

Route::get('/', [PalmMonitorController::class, 'index'])->name('palm-monitor.index');
Route::post('/api/polygons', [PalmMonitorController::class, 'store'])->name('api.polygons.store');
Route::get('/api/polygons', [PalmMonitorController::class, 'loadPolygons'])->name('api.polygons.load');
Route::get('/api/export/geojson', [PalmMonitorController::class, 'exportGeoJson'])->name('api.export.geojson');
Route::get('/api/export/csv', [PalmMonitorController::class, 'exportCsv'])->name('api.export.csv');
// In RouteServiceProvider
Route::middleware(['api', 'throttle:60,1'])->group(function () {
    // API routes
});
