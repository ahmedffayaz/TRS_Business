<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pakistan Rupee',
                'code' => 'PKR',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pound Sterling',
                'code' => 'GBP',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Saudi Riyal',
                'code' => 'SAR',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'UAE Dirham',
                'code' => 'AED',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'US Dollar',
                'code' => 'USD',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
