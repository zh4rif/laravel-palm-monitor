<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('palm_polygons', function (Blueprint $table) {
            $table->id();
            $table->string('license')->nullable();
            $table->string('smallholder')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('spoc_name')->nullable();
            $table->string('spoc_code')->nullable();
            $table->string('lot_no')->nullable();
            $table->decimal('certified_area', 10, 2)->nullable();
            $table->decimal('planted_area', 10, 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('mspo')->nullable();
            $table->string('land_title')->nullable();
            $table->decimal('shape_length', 15, 6)->nullable();
            $table->decimal('shape_area', 15, 12)->nullable();
            $table->json('geometry'); // Store GeoJSON geometry
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('palm_polygons');
    }
};
