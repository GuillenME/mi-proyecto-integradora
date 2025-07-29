<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxiDriver extends Model
{
    protected $table = 'taxi-driver';

    protected $primaryKey = 'id_taxista';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_taxista',
        'nombre',
        'edad',
        'ine',
        'permiso_taxi',
        'lincencia',
        'telefono',
        'contrasena',
        'idioma_id_idioma',
        'foto_conductor',
        'foto_taxi',
        'numero_cuenta',
    ];

    protected $hidden = [
        'contrasena',
    ];

    // Relación con idioma
    public function idioma()
    {
        return $this->belongsTo(Lenguage::class, 'idioma_id_idioma', 'id_idioma');
    }

    // Relación muchos a muchos con vehículos
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_has_taxi-driver', 'taxi-driver_id_taxista', 'vehicle_id_vehiculo')
                    ->withPivot('taxi-driver_idioma_id_idioma');
    }
}
