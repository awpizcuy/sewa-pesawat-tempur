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
        $unit1 = Unit::updateOrCreate(
            ['unit_code' => 'SU30-001'],
            [
                'name' => 'Sukhoi Su-30',
                'description' => 'Jet tempur superioritas udara multiperan buatan Rusia.',
                'stock' => 5,
                'status' => 'available',
                'price_per_day' => 7500000,
            ]
        );

        $unit1->categories()->syncWithoutDetaching([1,3]);

        $unit2 = Unit::updateOrCreate(
            ['unit_code' => 'AH64-APACHE'],
            [
                'name' => 'AH-64 Apache',
                'description' => 'Helikopter serbu utama Angkatan Darat AS.',
                'stock' => 3,
                'status' => 'available',
                'price_per_day' => 5200000,
            ]
        );

        $unit2->categories()->syncWithoutDetaching([2,3]);

        $unit3 = Unit::updateOrCreate(
            ['unit_code' => 'F35-LIGHTNING'],
            [
                'name' => 'F-35 Lightning II',
                'description' => 'Jet tempur siluman multiperan generasi kelima.',
                'stock' => 2,
                'status' => 'available',
                'price_per_day' => 9800000,
            ]
        );
        $unit3->categories()->syncWithoutDetaching([1,2]);

        // Tambahan unit agar total minimal 12, masing-masing 2 kategori
        $more = [
            ['code'=>'F16-BLOCK50','name'=>'F-16 Fighting Falcon','desc'=>'Jet tempur multirole ringan.','stock'=>4,'cats'=>[1,3],'price'=>4500000],
            ['code'=>'EUROFIGHTER','name'=>'Eurofighter Typhoon','desc'=>'Jet tempur multirole generasi 4.5.','stock'=>3,'cats'=>[1,3],'price'=>8000000],
            ['code'=>'RAFALE-F3R','name'=>'Dassault Rafale','desc'=>'Jet tempur multirole Perancis.','stock'=>2,'cats'=>[1,3],'price'=>8200000],
            ['code'=>'MIG29-UB','name'=>'MiG-29','desc'=>'Jet tempur taktis Rusia.','stock'=>3,'cats'=>[1,3],'price'=>3800000],
            ['code'=>'SU35S','name'=>'Sukhoi Su-35S','desc'=>'Jet tempur supermaneuverable Rusia.','stock'=>2,'cats'=>[1,3],'price'=>9000000],
            ['code'=>'A10C-THUNDER','name'=>'A-10C Thunderbolt II','desc'=>'Pesawat serang darat.','stock'=>2,'cats'=>[1,3],'price'=>3000000],
            ['code'=>'C130J-SUPER','name'=>'C-130J Super Hercules','desc'=>'Pesawat angkut taktis.','stock'=>2,'cats'=>[3,2],'price'=>6000000],
            ['code'=>'CH47F-CHINOOK','name'=>'CH-47F Chinook','desc'=>'Helikopter angkut berat tandem.','stock'=>2,'cats'=>[2,3],'price'=>5500000],
            ['code'=>'UH60M-BLACK','name'=>'UH-60M Black Hawk','desc'=>'Helikopter utilitas.','stock'=>3,'cats'=>[2,3],'price'=>3200000],
        ];
        foreach($more as $m){
            $u = Unit::updateOrCreate(
                ['unit_code'=>$m['code']],
                [
                    'name'=>$m['name'],
                    'description'=>$m['desc'],
                    'stock'=>$m['stock'],
                    'status'=>'available',
                    'price_per_day'=>$m['price'],
                ]
            );
            $u->categories()->syncWithoutDetaching($m['cats']);
        }
    }
}
