<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_has_taxi-driver', function (Blueprint $table) {
            $table->integer('vehicle_id_vehiculo');
            $table->integer('taxi-driver_id_taxista');
            $table->integer('taxi-driver_idioma_id_idioma');

            // Primary key compuesta
            $table->primary(['vehicle_id_vehiculo', 'taxi-driver_id_taxista', 'taxi-driver_idioma_id_idioma']);

            // Foreign keys
            $table->foreign('vehicle_id_vehiculo')->references('id_vehiculo')->on('vehicle');
            $table->foreign(['taxi-driver_id_taxista', 'taxi-driver_idioma_id_idioma'])
                  ->references(['id_taxista', 'idioma_id_idioma'])
                  ->on('taxi-driver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_has_taxi-driver');
    }
};
