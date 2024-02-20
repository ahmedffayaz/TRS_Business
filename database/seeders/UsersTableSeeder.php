<?php

namespace Database\Seeders;

use App\Enums\User\AccountType;
use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Str;
use App\Enums\User\UserStatus;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Factories\UserFactory;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$superAdmin = User::create([
			'first_name' => 'TRS',
            'last_name' => 'Admin',
            'email' => 'superadmin@trs.com',
            'email_verified_at' => now(),
            'designation' => 'CEO',
            'phone' => '+17026723124',
            'password' => 'admin',
			'address' => 'Islamabad',
            'is_active' => UserStatus::ACTIVE,
            'remember_token' => Str::random(10),
            'account_type' => AccountType::BUSINESS->value,
            'client_id' => null
		])->assignRole('super-admin');

        $admin = User::create([
			'first_name' => 'TRS',
            'last_name' => 'Admin',
            'email' => 'admin@trs.com',
            'email_verified_at' => now(),
            'designation' => 'Admin',
            'phone' => '+17026723124',
            'password' => 'admin',
			'address' => 'Islamabad',
            'is_active' => UserStatus::ACTIVE,
            'remember_token' => Str::random(10),
            'account_type' => AccountType::BUSINESS->value,
            'client_id' => null
		])->assignRole('admin');

		UserFactory::new()->count(10)->create()->each(function ($user) {
			$user->assignRole('project-manager');
		});
		UserFactory::new()->count(10)->create()->each(function ($user) {
			$user->assignRole(['team-lead', 'developer']);
		});
		UserFactory::new()->count(10)->create()->each(function ($user) {
			$user->assignRole('developer');
		});
		UserFactory::new()->count(10)->create()->each(function ($user) {
			$roles = ['client', 'quality-insurance'];
			$user->assignRole($roles[mt_rand(0, count($roles) - 1)]);
		});
	}
}
