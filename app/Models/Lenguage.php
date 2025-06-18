<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lenguage extends Model
{
    protected $table = 'lenguage';

    protected $primaryKey = 'id_idioma';

    public $timestamps = false;

    protected $fillable = [
        'id_idioma',
        'tipo',
    ];

    public function passengers()
    {
        return $this->hasMany(Passenger::class, 'idioma_id_idioma', 'id_idioma');
    }
}
