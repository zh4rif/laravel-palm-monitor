<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PalmPolygon extends Model
{
    protected $fillable = [
        'license', 'smallholder', 'state', 'district', 'subdistrict',
        'spoc_name', 'spoc_code', 'lot_no', 'certified_area', 'planted_area',
        'latitude', 'longitude', 'mspo', 'land_title', 'shape_length',
        'shape_area', 'geometry'
    ];

    protected $casts = [
        'geometry' => 'array',
        'certified_area' => 'decimal:2',
        'planted_area' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'shape_length' => 'decimal:6',
        'shape_area' => 'decimal:12',
    ];
}
