<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'id_vehiculo' => 1,
                'placa' => 'ABC123',
                'num_taxi' => 101,
                'modelo' => 'Sentra',
                'marca' => 'Nissan',
                'numero_serie' => 'NS123456789',
                'anio' => '2020'
            ],
            [
                'id_vehiculo' => 2,
                'placa' => 'DEF456',
                'num_taxi' => 102,
                'modelo' => 'Versa',
                'marca' => 'Nissan',
                'numero_serie' => 'NS987654321',
                'anio' => '2021'
            ],
            [
                'id_vehiculo' => 3,
                'placa' => 'GHI789',
                'num_taxi' => 103,
                'modelo' => 'Aveo',
                'marca' => 'Chevrolet',
                'numero_serie' => 'CH123789456',
                'anio' => '2019'
            ],
            [
                'id_vehiculo' => 4,
                'placa' => 'JKL012',
                'num_taxi' => 104,
                'modelo' => 'Spark',
                'marca' => 'Chevrolet',
                'numero_serie' => 'CH456789123',
                'anio' => '2022'
            ],
            [
                'id_vehiculo' => 5,
                'placa' => 'MNO345',
                'num_taxi' => 105,
                'modelo' => 'March',
                'marca' => 'Nissan',
                'numero_serie' => 'NS789123456',
                'anio' => '2021'
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
