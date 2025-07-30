<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_has_taxi_driver', function (Blueprint $table) {
            $table->integer('vehicle_id_vehiculo');
            $table->integer('taxi_driver_id_taxista');
            $table->unsignedBigInteger('taxi_driver_idioma_id_idioma');

            // Clave primaria compuesta
            $table->primary([
                'vehicle_id_vehiculo',
                'taxi_driver_id_taxista',
                'taxi_driver_idioma_id_idioma'
            ]);

            // Claves foráneas
            $table->foreign('vehicle_id_vehiculo')
                  ->references('id_vehiculo')->on('vehicle');

            $table->foreign(
                ['taxi_driver_id_taxista', 'taxi_driver_idioma_id_idioma'],
                'fk_vehicle_taxi_driver'
            )->references(['id_taxista', 'idioma_id_idioma'])
             ->on('taxi_driver');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_has_taxi_driver');
    }
};
