<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Company\CompanyType;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'The Right Software',
                'street_address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
                'city' => 'Rawalpindi',
                'country_id' => 1,
                'postal_code' => '43600',
                'type' => CompanyType::PARENT->value,
                'invoice_prefix' => 'trs_',
            ],
            [
                'name' => 'Dev Provider',
                'street_address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
                'city' => 'Rawalpindi',
                'country_id' => 1,
                'postal_code' => '43600',
                'type' => CompanyType::PARENT->value,
                'invoice_prefix' => 'dev_pro_',
            ]
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(
                [
                    'name' => $company['name'],
                    'city' => $company['city']
                ],
                $company
            );
        }

        Company::factory(10)->create();
    }
}
