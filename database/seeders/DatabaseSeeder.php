<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		$this->call([
            SettingTableSeeder::class,
			CountrySeeder::class,
            CompaniesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            BusinessSeeder::class,
            ClientSeeder::class,
            UsersTableSeeder::class,
			CurrencySeeder::class,
			ProjectsTableSeeder::class,
			TasksTableSeeder::class,
			CommentsTableSeeder::class,
			KnowledgeBaseSeeder::class,
        ]);
	}
}
