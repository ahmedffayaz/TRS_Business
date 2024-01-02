<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use App\Enums\User\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@demo.com',
            'email_verified_at' => now(),
            'designation' => 'CEO',
            'phone' => '+17026723124',
            'password' => bcrypt('admin'),
            'is_active' => UserStatus::ACTIVE,
            'remember_token' => Str::random(10)
        ]);
    }
}
