<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxi_driver', function (Blueprint $table) {
            $table->integer('id_taxista')->primary();
            $table->string('nombre', 100)->nullable();
            $table->integer('edad')->nullable();
            $table->string('ine', 45)->nullable();
            $table->string('permiso_taxi', 45)->nullable();
            $table->string('lincencia', 45)->nullable();
            $table->string('telefono', 45)->nullable();
            $table->string('contrasena', 45)->nullable();

            // 🔧 Clave foránea corregida
            $table->unsignedBigInteger('idioma_id_idioma');
            $table->foreign('idioma_id_idioma')->references('id_idioma')->on('lenguage');

            $table->string('foto_conductor', 45)->nullable();
            $table->string('foto_taxi', 45)->nullable();
            $table->integer('numero_cuenta')->nullable();

            // Índice único compuesto para permitir clave foránea compuesta
            $table->unique(['id_taxista', 'idioma_id_idioma'], 'unique_taxi_driver_composite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxi_driver');
    }
};
