<?php

namespace Database\Seeders;

use App\Enums\User\UserStatus;
use App\Models\Company;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		User::create([
			'first_name' => 'TRS',
            'last_name' => 'Admin',
            'email' => 'admin@demo.com',
            'email_verified_at' => now(),
            'designation' => 'CEO',
            'phone' => '+17026723124',
            'password' => 'admin',
			'address' => 'Islamabad',
            'is_active' => UserStatus::ACTIVE,
            'remember_token' => Str::random(10),
			'company_id' => Company::first()->id
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
