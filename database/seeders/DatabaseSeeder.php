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
            BusinessSeeder::class,
            RoleSeeder::class,
            PermissionsSeeder::class,
            ClientSeeder::class,
            UserSeeder::class,
			CurrencySeeder::class,
            KnowledgeBaseCategorySeeder::class,
			ProjectSeeder::class,
			TaskSeeder::class,
			CommentSeeder::class,
            EmailTemplateSeeder::class,
        ]);
	}
}
