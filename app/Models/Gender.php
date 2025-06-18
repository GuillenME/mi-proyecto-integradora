<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'gender';

    protected $primaryKey = 'id_genero';

    public $timestamps = false;

    protected $fillable = [
        'id_genero',
        'tipo',
    ];

    public function passengers()
    {
        return $this->hasMany(Passenger::class, 'genero_id_genero', 'id_genero');
    }
}
