<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     */
    public function run(): void
    {
        $unit1 = Unit::create([
            'unit_code' => 'SU30-001',
            'name' => 'Sukhoi Su-30',
            'description' => 'Jet tempur superioritas udara multiperan buatan Rusia.',
            'stock' => 5,
            'status' => 'available',
        ]);

        $unit1->categories()->attach([1]);

        $unit2 = Unit::create([
            'unit_code' => 'AH64-APACHE',
            'name' => 'AH-64 Apache',
            'description' => 'Helikopter serbu utama Angkatan Darat AS.',
            'stock' => 3,
            'status' => 'available',
        ]);

        $unit2->categories()->attach([2]);

        $unit3 = Unit::create([
            'unit_code' => 'F35-LIGHTNING',
            'name' => 'F-35 Lightning II',
            'description' => 'Jet tempur siluman multiperan generasi kelima.',
            'stock' => 2,
            'status' => 'available',
        ]);
        $unit3->categories()->attach([1]);
    }
}
