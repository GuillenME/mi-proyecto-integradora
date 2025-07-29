<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';

    protected $primaryKey = 'id_vehiculo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_vehiculo',
        'placa',
        'num_taxi',
        'modelo',
        'marca',
        'numero_serie',
        'anio',
    ];

    // Relación muchos a muchos con taxistas
    public function taxiDrivers()
    {
        return $this->belongsToMany(TaxiDriver::class, 'vehicle_has_taxi-driver', 'vehicle_id_vehiculo', 'taxi-driver_id_taxista')
                    ->withPivot('taxi-driver_idioma_id_idioma');
    }
}
