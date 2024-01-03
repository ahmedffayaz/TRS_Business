<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		$this->call([
            SettingTableSeeder::class,
            CountriesTableSeeder::class,
            CompaniesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            ClientSeeder::class,
			CountrySeeder::class,
			CurrencySeeder::class,
			ProjectsTableSeeder::class,
			TasksTableSeeder::class,
			CommentsTableSeeder::class,
			KnowledgeBaseTableSeeder::class,
        ]);
	}
}
