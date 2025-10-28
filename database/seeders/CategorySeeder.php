<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Jet Tempur',
            'slug' => 'jet-tempur'
        ]);

        Category::create([
            'name' => 'Heli Serbu',
            'slug' => 'heli-serbu'
        ]);

        Category::create([
            'name' => 'Pesawat Angkut',
            'slug' => 'pesawat-angkut'
        ]);

    }
}
