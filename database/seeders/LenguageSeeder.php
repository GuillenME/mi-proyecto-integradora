<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lenguage;

class LenguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['id_idioma' => 1, 'tipo' => 'Español'],
            ['id_idioma' => 2, 'tipo' => 'Inglés'],
            ['id_idioma' => 3, 'tipo' => 'Francés'],
            ['id_idioma' => 4, 'tipo' => 'Alemán'],
            ['id_idioma' => 5, 'tipo' => 'Italiano'],
            ['id_idioma' => 6, 'tipo' => 'Portugués'],
        ];

        foreach ($languages as $language) {
            Lenguage::create($language);
        }
    }
}
