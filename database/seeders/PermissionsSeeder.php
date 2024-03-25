<?php

namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Schema::disableForeignKeyConstraints();
		DB::table('permissions')->truncate();
		Schema::enableForeignKeyConstraints();

	    // Reset cached roles and permissions
	    app()['cache']->forget('spatie.permission.cache');
        // Businesses
	    Permission::create(['group' => 'business', 'name' => 'add_businesses', 'title' => 'Add businesses']);
	    Permission::create(['group' => 'business', 'name' => 'edit_businesses', 'title' => 'Edit businesses']);
	    Permission::create(['group' => 'business', 'name' => 'view_businesses', 'title' => 'View businesses']);
	    Permission::create(['group' => 'business', 'name' => 'delete_businesses', 'title' => 'Delete businesses']);

        // Businesses
	    Permission::create(['group' => 'clients', 'name' => 'add_clients', 'title' => 'Add clients']);
	    Permission::create(['group' => 'clients', 'name' => 'edit_clients', 'title' => 'Edit clients']);
	    Permission::create(['group' => 'clients', 'name' => 'view_clients', 'title' => 'View clients']);
	    Permission::create(['group' => 'clients', 'name' => 'delete_clients', 'title' => 'Delete clients']);

	    // Users
	    Permission::create(['group' => 'user', 'name' => 'add_users', 'title' => 'Add users']);
	    Permission::create(['group' => 'user', 'name' => 'edit_users', 'title' => 'Edit users']);
	    Permission::create(['group' => 'user', 'name' => 'view_users', 'title' => 'View users']);
	    Permission::create(['group' => 'user', 'name' => 'delete_users', 'title' => 'Delete users']);
	    Permission::create(['group' => 'user', 'name' => 'view_contracts', 'title' => 'View contracts']);
	    Permission::create(['group' => 'user', 'name' => 'download_contracts', 'title' => 'Download contracts']);
	    // Settings
	    Permission::create(['group' => 'settings', 'name' => 'manage_system_settings', 'title' => 'Manage system settings']);
	    // Roles
	    Permission::create(['group' => 'role', 'name' => 'add_roles', 'title' => 'Add roles']);
	    Permission::create(['group' => 'role', 'name' => 'edit_roles', 'title' => 'Edit roles']);
	    Permission::create(['group' => 'role', 'name' => 'view_roles', 'title' => 'View roles']);
	    Permission::create(['group' => 'role', 'name' => 'delete_roles', 'title' => 'Delete roles']);
	    Permission::create(['group' => 'role', 'name' => 'view_permissions', 'title' => 'View permissions']);
	    // Projects
	    Permission::create(['group' => 'project', 'name' => 'add_projects', 'title' => 'Add projects']);
	    Permission::create(['group' => 'project', 'name' => 'edit_projects', 'title' => 'Edit projects']);
	    Permission::create(['group' => 'project', 'name' => 'view_projects', 'title' => 'View projects']);
	    Permission::create(['group' => 'project', 'name' => 'delete_projects', 'title' => 'Delete projects']);
	    Permission::create(['group' => 'project', 'name' => 'assign_member', 'title' => 'Assign member to project']);
	    Permission::create(['group' => 'project', 'name' => 'remove_member', 'title' => 'Remove member from project']);
	    Permission::create(['group' => 'project', 'name' => 'view_budget', 'title' => 'View budget']);
	    Permission::create(['group' => 'project', 'name' => 'deliver_project', 'title' => 'Deliver project']);
	    Permission::create(['group' => 'project', 'name' => 'view_revenue', 'title' => 'View revenue']);
		Permission::create(['group' => 'project', 'name' => 'view_archived', 'title' => 'View archived']);
		Permission::create(['group' => 'project', 'name' => 'view_associated_projects', 'title' => 'View Associated Projects']);

	    // Tasks
	    Permission::create(['group' => 'task', 'name' => 'add_tasks', 'title' => 'Add tasks']);
	    Permission::create(['group' => 'task', 'name' => 'edit_tasks', 'title' => 'Edit tasks']);
	    Permission::create(['group' => 'task', 'name' => 'view_tasks', 'title' => 'View tasks']);
	    Permission::create(['group' => 'task', 'name' => 'delete_tasks', 'title' => 'Delete tasks']);
	    Permission::create(['group' => 'task', 'name' => 'mark_completed', 'title' => 'Mark completed']);
	    // Comments
	    Permission::create(['group' => 'comment', 'name' => 'view_comments', 'title' => 'View comments']);
	    Permission::create(['group' => 'comment', 'name' => 'add_comments', 'title' => 'Add comments']);
	    Permission::create(['group' => 'comment', 'name' => 'edit_comments', 'title' => 'Edit comments']);
	    Permission::create(['group' => 'comment', 'name' => 'delete_comments', 'title' => 'Delete comments']);
		// Contract Types
	    Permission::create(['group' => 'terms_conditions', 'name' => 'add_terms_conditions', 'title' => 'Add terms & conditions']);
	    Permission::create(['group' => 'terms_conditions', 'name' => 'edit_terms_conditions', 'title' => 'Edit terms & conditions']);
	    Permission::create(['group' => 'terms_conditions', 'name' => 'view_terms_conditions', 'title' => 'View terms & conditions']);
	    Permission::create(['group' => 'terms_conditions', 'name' => 'delete_terms_conditions', 'title' => 'Delete terms & conditions']);
		Permission::create(['group' => 'terms_conditions', 'name' => 'download_terms_conditions', 'title' => 'download terms and conditions types'
		]);
	    // Invoices
	    Permission::create(['group' => 'invoice', 'name' => 'add_invoices', 'title' => 'Generate invoices']);
	    Permission::create(['group' => 'invoice', 'name' => 'view_invoices', 'title' => 'View invoices']);
	    Permission::create(['group' => 'invoice', 'name' => 'bill_invoices', 'title' => 'Bill invoices']);
	    // FAQS
        Permission::create(['group' => 'knowledgeBase', 'name' => 'add_knowledgeBase', 'title' => 'Add knowledge base']);
        Permission::create(['group' => 'knowledgeBase', 'name' => 'edit_knowledgeBase', 'title' => 'Edit knowledge base']);
        Permission::create(['group' => 'knowledgeBase', 'name' => 'view_knowledgeBase', 'title' => 'View knowledge base']);
        Permission::create(['group' => 'knowledgeBase', 'name' => 'delete_knowledgeBase', 'title' => 'Delete knowledge base']);
        // Attachments
        Permission::create(['group' => 'attachment', 'name' => 'add_attachments', 'title' => 'Add attachment']);
        Permission::create(['group' => 'attachment', 'name' => 'view_attachments', 'title' => 'View attachments']);
        Permission::create(['group' => 'attachment', 'name' => 'delete_attachments', 'title' => 'Delete attachment']);
        // Attendance
        Permission::create(['group' => 'attendance', 'name' => 'add_attendance', 'title' => 'Add attendance']);
        Permission::create(['group' => 'attendance', 'name' => 'edit_attendance', 'title' => 'Edit attendance']);
        Permission::create(['group' => 'attendance', 'name' => 'view_attendance', 'title' => 'View attendance']);
		Permission::create(['group' => 'attendance', 'name' => 'delete_attendance', 'title' => 'Delete attendance']);
		Permission::create(['group' => 'attendance', 'name' => 'mark_attendance', 'title' => 'Mark attendance']);
		// system
	    Permission::create(['group' => 'system', 'name' => 'add_systems', 'title' => 'Add systems']);
	    Permission::create(['group' => 'system', 'name' => 'edit_systems', 'title' => 'Edit systems']);
	    Permission::create(['group' => 'system', 'name' => 'delete_systems', 'title' => 'Delete systems']);
	    Permission::create(['group' => 'system', 'name' => 'view_systems', 'title' => 'View permissions']);
		//leave
        Permission::create(['group' => 'leave', 'name' => 'approve_leaves', 'title' => 'Approve leave']);
        Permission::create(['group' => 'leave', 'name' => 'add_leaves', 'title' => 'Add leave']);
        Permission::create(['group' => 'leave', 'name' => 'delete_leaves', 'title' => 'Delete leave']);
        Permission::create(['group' => 'leave', 'name' => 'view_leaves', 'title' => 'View leave']);
        // project new field add
        Permission::create(['group' => 'project', 'name' => 'view_statistics', 'title' => 'View Statistics']);
		Permission::create(['group' => 'leave', 'name' => 'add_employees_leaves', 'title' => 'Add Employees Leaves']);

        // Email permissions
        Permission::create(['group' => 'email', 'name' => 'email_reports', 'title' => 'Email Reports']);

        $sudperAdminRole = Role::updateOrCreate(['name' => 'super-admin'], ['title' => 'Super Admin']);
		$sudperAdminRole->permissions()->sync(Permission::pluck('id')->all());

    }
}
