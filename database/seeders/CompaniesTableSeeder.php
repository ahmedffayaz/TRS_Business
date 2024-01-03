<?php

namespace Database\Seeders;

use App\Enums\Company\CompanyType;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\CompanyFactory;

class CompaniesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// TRS
		$companies = [
			[
				'name' => 'The Right Software',
				'street_address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
				'address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
				'city' => 'Rawalpindi',
				'country_id' => 1,
				'postal_code' => '43600',
				'type' => CompanyType::PARENT->value,
				'invoice_prefix' => 'trs_',
			],
			[
				'name' => 'Dev Provider',
				'street_address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
				'address' => 'Office 6-B1, Silk Center, Rehmanabad Metro Stop, Murree Road',
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

		CompanyFactory::new()->count(10)->create();
	}
}
