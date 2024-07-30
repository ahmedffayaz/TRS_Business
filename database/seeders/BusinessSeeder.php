<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Client;
use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\Business\BusinessType;
use Illuminate\Support\Facades\Schema;
use Database\Factories\BusinessFactory;
use Database\Factories\ClientFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Business::truncate();
        DB::table('business_role')->truncate();
        Schema::enableForeignKeyConstraints();

        // TRS
		$businesses = array(
			array(
				'name' => 'The Right Software',
                'slug' => 'the-right-software',
				'address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
				'city' => 'Rawalpindi',
				'country_id' => 1,
				'postal_code' => '43600',
				'invoice_prefix' => 'trs_',
                'invoice_serial' => 'dks78ds',
                'created_at' => now(),
                'updated_at' => now()
            ),
			array(
				'name' => 'Dev Provider',
                'slug' => 'dev-provider',
				'address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
				'city' => 'Rawalpindi',
				'country_id' => 1,
				'postal_code' => '43600',
				'invoice_prefix' => 'dev_pro_',
                'invoice_serial' => 'd25s78ds',
                'created_at' => now(),
                'updated_at' => now()
            )
        );

        Business::insert($businesses);


        $businessFactory = BusinessFactory::new()->count(10)->create();

        // For each business, create some clients
        $businessFactory->each(function ($business) {
            ClientFactory::new()->count(10)->create();
        });
    }
}
