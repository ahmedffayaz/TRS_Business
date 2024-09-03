<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Str;
use App\Enums\User\UserStatus;
use App\Enums\User\AccountType;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
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
            'business_id' => null,
            'client_id' => null
        ])->assignRole('super-admin');

        // Assuming you have predefined roles for each business
        $roles = Role::whereIn('name', ['admin', 'project-manager', 'team-lead', 'developer', 'client', 'quality-insurance'])->get();

        $businesses = Business::all();

        foreach ($businesses as $business) {
            // Create Admin user for each business
            $admin = User::create([
                'first_name' => 'TRS',
                'last_name' => 'Admin',
                'email' => 'admin_' . $business->id . '@trs.com',
                'email_verified_at' => now(),
                'designation' => 'Admin',
                'phone' => '+17026723124',
                'password' => 'password',
                'address' => 'Islamabad',
                'is_active' => UserStatus::ACTIVE->value,
                'remember_token' => Str::random(10),
                'account_type' => AccountType::BUSINESS->value,
                'business_id' => $business->id,
                'client_id' => $business->clients()->first()->id,
            ]);

            // Assign 'admin' role to the admin user
            $adminRole = Role::where('business_id', $business->id)->where('name', 'admin')->first();
            DB::table('model_has_roles')->insert([
                'role_id' => $adminRole->id,
                'model_type' => User::class,
                'model_id' => $admin->id,
            ]);

            // Create Project Managers
            User::factory()->count(1)->create()->each(function ($user) use ( $business) {
                $pmRole = Role::where('business_id', $business->id)->where('name', 'project-manager')->first();
                    DB::table('model_has_roles')->insert([
                        'role_id' => $pmRole->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                    ]);
            });

            // Create Team Leads
            User::factory()->count(1)->create()->each(function ($user) use ( $business) {
                $teamLeadRole = Role::where('business_id', $business->id)->where('name', 'team-lead')->first();
                    DB::table('model_has_roles')->insert([
                        'role_id' => $teamLeadRole->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                    ]);
            });

            // Create Developers
            User::factory()->count(5)->create()->each(function ($user) use ( $business) {
                $developerRole = Role::where('business_id', $business->id)->where('name', 'developer')->first();
                    DB::table('model_has_roles')->insert([
                        'role_id' => $developerRole->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                    ]);
            });

            // Create Clients
            User::factory()->count(1)->create()->each(function ($user) use ( $business) {
                $clientRole = Role::where('business_id', $business->id)->where('name', 'client')->first();
                    DB::table('model_has_roles')->insert([
                        'role_id' => $clientRole->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                    ]);
            });

            // Create Quality Assurance
            User::factory()->count(1)->create()->each(function ($user) use ( $business) {
                $qualityRole = Role::where('business_id', $business->id)->where('name', 'quality-insurance')->first();
                    DB::table('model_has_roles')->insert([
                        'role_id' => $qualityRole->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                    ]);
            });
        }
    }
}
