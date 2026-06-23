<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // Municípios do Alto Vale do Itajaí (SC), região atendida pela feira.
        $cities = [
            'Agronômica',
            'Aurora',
            'Ibirama',
            'Ituporanga',
            'Laurentino',
            'Lontras',
            'Pouso Redondo',
            'Presidente Getúlio',
            'Rio do Oeste',
            'Rio do Sul',
            'Taió',
            'Trombudo Central',
        ];

        foreach ($cities as $name) {
            City::firstOrCreate(['name' => $name]);
        }
    }
}
