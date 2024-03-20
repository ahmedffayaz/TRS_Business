<?php

namespace Database\Seeders;
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

		$role = Role::create(['name' => 'super-admin', 'title' => 'Super Admin', 'is_deletable' => 0]);
		$role->permissions()->sync(Permission::pluck('id')->all());
		Role::create(['name' => 'admin', 'title' => 'Admin', 'is_deletable' => 0]);
		Role::create(['name' => 'client', 'title' => 'Client', 'is_deletable' => 0]);
		Role::create(['name' => 'developer', 'title' => 'Developer', 'is_deletable' => 0]);
		Role::create(['name' => 'team-lead', 'title' => 'Team Lead', 'is_deletable' => 0]);
		Role::create(['name' => 'quality-insurance', 'title' => 'Quality Insurance', 'is_deletable' => 0]);
		Role::create(['name' => 'finance', 'title' => 'Finance', 'is_deletable' => 0]);
		Role::create(['name' => 'project-manager', 'title' => 'Project Manager', 'is_deletable' => 0]);
		Role::create(['name' => 'sales', 'title' => 'Sales', 'is_deletable' => 0]);
		Role::create(['name' => 'Guest Developer', 'title' => 'Guest Developer', 'is_deletable' => 1]);
		Role::create(['name' => 'KBOnly', 'title' => 'KBOnly', 'is_deletable' => 1]);
	}
}
