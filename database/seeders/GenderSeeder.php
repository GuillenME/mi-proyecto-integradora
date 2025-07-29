<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = [
            ['id_genero' => 1, 'tipo' => 'Masculino'],
            ['id_genero' => 2, 'tipo' => 'Femenino'],
            ['id_genero' => 3, 'tipo' => 'No binario'],
            ['id_genero' => 4, 'tipo' => 'Prefiero no decir'],
        ];

        foreach ($genders as $gender) {
            Gender::create($gender);
        }
    }
}
