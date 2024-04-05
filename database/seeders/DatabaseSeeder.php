<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		$this->call([
            SettingSeeder::class,
            EmailTemplateSeeder::class,
			CountrySeeder::class,
            PermissionsSeeder::class,
            RoleSeeder::class,
            BusinessSeeder::class,
            ClientSeeder::class,
            UserSeeder::class,
			CurrencySeeder::class,
            KnowledgeBaseCategorySeeder::class,
			ProjectSeeder::class,
			TaskSeeder::class,
			CommentSeeder::class,
        ]);
	}
}
