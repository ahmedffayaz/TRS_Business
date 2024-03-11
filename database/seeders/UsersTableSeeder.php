<?php

namespace Database\Seeders;

use App\Enums\User\AccountType;
use App\Models\User;
use Illuminate\Support\Str;
use App\Enums\User\UserStatus;
use Illuminate\Database\Seeder;
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
            'password' => 'password',
			'address' => 'Islamabad',
            'is_active' => UserStatus::ACTIVE->value,
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
            'password' => 'password',
			'address' => 'Islamabad',
            'is_active' => UserStatus::ACTIVE->value,
            'remember_token' => Str::random(10),
            'account_type' => AccountType::BUSINESS->value,
            'client_id' => 2
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
