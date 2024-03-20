<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		$this->call([
            SettingSeeder::class,
			CountrySeeder::class,
            PermissionsSeeder::class,
            RoleSeeder::class,
            BusinessSeeder::class,
            ClientSeeder::class,
            UserSeeder::class,
			CurrencySeeder::class,
			ProjectSeeder::class,
			TaskSeeder::class,
			CommentsTableSeeder::class,
        ]);
	}
}
