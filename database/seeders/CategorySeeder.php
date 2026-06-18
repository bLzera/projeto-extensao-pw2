<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Frutas', 'Verduras', 'Legumes', 'Laticínios', 'Ovos',
            'Mel e derivados', 'Grãos e cereais', 'Conservas', 'Artesanato', 'Outros',
        ];

        $rows = array_map(fn($name) => [
            'name' => $name,
            'slug' => Str::slug($name),
        ], $categories);

        DB::table('categories')->upsert($rows, ['slug'], ['name']);
    }
}
