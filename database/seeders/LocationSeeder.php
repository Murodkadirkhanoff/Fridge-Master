<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            [
                'name' => 'Уилмингтон (Северная Каролина)',
                'timezone' => 'GMT-4'
            ],
            [
                'name' => 'Портленд (Орегон)',
                'timezone' => 'GMT-7'
            ],
            [
                'name' => 'Торонто',
                'timezone' => 'GMT-4'
            ],
            [
                'name' => 'Торонто',
                'timezone' => 'GMT-4'
            ],
            [
                'name' => 'Варшава',
                'timezone' => 'GMT+2'
            ],
            [
                'name' => 'Шанхай',
                'timezone' => 'GMT+8'
            ],
        ];

        foreach ($locations as $location) {
            DB::table('locations')->insert([
                'name' => $location['name'],
                'timezone' => $location['timezone'],
                'is_active' => 1
            ]);
        }

    }
}
