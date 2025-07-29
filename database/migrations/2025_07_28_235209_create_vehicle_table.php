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
        Schema::create('vehicle', function (Blueprint $table) {
            $table->integer('id_vehiculo')->primary();
            $table->string('placa', 45)->nullable();
            $table->integer('num_taxi')->nullable();
            $table->string('modelo', 45)->nullable();
            $table->string('marca', 45)->nullable();
            $table->string('numero_serie', 45)->nullable();
            $table->string('anio', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle');
    }
};
