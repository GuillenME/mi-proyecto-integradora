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
        Schema::table('passenger', function (Blueprint $table) {
            // Agregar campos faltantes
            $table->string('ine_imagen')->nullable()->after('foto');
            $table->unsignedBigInteger('genero_id_genero')->nullable()->after('contrasena');
            $table->unsignedBigInteger('idioma_id_idioma')->nullable()->after('genero_id_genero');
            $table->string('discapacidad')->nullable()->after('idioma_id_idioma');
            $table->integer('numerocuenta')->nullable()->after('discapacidad');
            $table->integer('fechaexpiracion')->nullable()->after('numerocuenta');
            $table->integer('cvv')->nullable()->after('fechaexpiracion');

            // Cambiar el tipo de telefono a string para ser consistente
            $table->string('telefono')->nullable()->change();

            // Agregar foreign keys
            $table->foreign('genero_id_genero')->references('id_genero')->on('gender');
            $table->foreign('idioma_id_idioma')->references('id_idioma')->on('lenguage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passenger', function (Blueprint $table) {
            // Remover foreign keys
            $table->dropForeign(['genero_id_genero']);
            $table->dropForeign(['idioma_id_idioma']);

            // Remover columnas agregadas
            $table->dropColumn([
                'ine_imagen',
                'genero_id_genero',
                'idioma_id_idioma',
                'discapacidad',
                'numerocuenta',
                'fechaexpiracion',
                'cvv'
            ]);

            // Revertir telefono a integer
            $table->integer('telefono')->change();
        });
    }
};
