<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		$this->call([
            SettingsSeeder::class,
			CountrySeeder::class,
            CompaniesTableSeeder::class,
            PermissionsSeeder::class,
            RolesTableSeeder::class,
            BusinessSeeder::class,
            ClientSeeder::class,
            UsersTableSeeder::class,
			CurrencySeeder::class,
			ProjectsSeeder::class,
			TasksTableSeeder::class,
			CommentsTableSeeder::class,
        ]);
	}
}
