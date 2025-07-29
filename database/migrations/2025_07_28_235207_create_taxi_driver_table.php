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
        Schema::create('taxi-driver', function (Blueprint $table) {
            $table->integer('id_taxista')->primary();
            $table->string('nombre', 100)->nullable();
            $table->integer('edad')->nullable();
            $table->string('ine', 45)->nullable();
            $table->string('permiso_taxi', 45)->nullable();
            $table->string('lincencia', 45)->nullable();
            $table->string('telefono', 45)->nullable();
            $table->string('contrasena', 45)->nullable();
            $table->integer('idioma_id_idioma');
            $table->string('foto_conductor', 45)->nullable();
            $table->string('foto_taxi', 45)->nullable();
            $table->integer('numero_cuenta')->nullable();

            // Foreign key
            $table->foreign('idioma_id_idioma')->references('id_idioma')->on('lenguage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxi-driver');
    }
};
