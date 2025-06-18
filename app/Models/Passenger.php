<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $table = 'passenger'; // Nombre exacto de la tabla en la base de datos

    protected $primaryKey = 'id_usuario'; // Llave primaria personalizada
    public $incrementing = false; // No es autoincremental
    public $timestamps = false; // Desactiva created_at y updated_at

    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellidos',
        'ine',
        'foto',
        'correo',
        'telefono',
        'contrasena',
        'genero_id_genero',
        'idioma_id_idioma',
        'discapacidad',
        'numerocuenta',
        'fechaexpiracion',
        'cvv',
    ];

    protected $hidden = [
        'contrasena',
        'cvv',
    ];

    // Relaciones (si necesitas acceder a los datos del idioma y género)
    public function genero()
    {
        return $this->belongsTo(Gender::class, 'genero_id_genero', 'id_genero');
    }

    public function idioma()
    {
        return $this->belongsTo(Lenguage::class, 'idioma_id_idioma', 'id_idioma');
    }
}
