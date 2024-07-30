<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        Schema::disableForeignKeyConstraints();
		DB::table('roles')->truncate();
		Schema::enableForeignKeyConstraints();

		Role::create(['name' => 'super-admin', 'title' => 'Super Admin', 'is_deletable' => 0]);

        $businesses = Business::all();

        $roles = [];
        foreach ($businesses as $business) {
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'admin','title' => 'Admin', 'is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'client', 'title' => 'Client', 'is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'developer', 'title' => 'Developer','is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'team-lead', 'title' => 'Team Lead', 'is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'quality-insurance', 'title' => 'Quality Insurance', 'is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'finance', 'title' => 'Finance', 'is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'project-manager', 'title' => 'Project Manager', 'is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'sales', 'title' => 'Sales', 'is_deletable' => 0];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'Guest Developer','title' => 'Guest Developer','is_deletable' => 1];
            $roles[] = ['business_id' => $business->id,  'guard_name' => 'web',  'name' => 'KBOnly', 'title' => 'KBOnly', 'is_deletable' => 0];
        }

        Role::insert($roles);
	}
}
