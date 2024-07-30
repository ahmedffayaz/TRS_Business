<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use App\Models\Task;
use App\Models\User;
// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use App\Models\Client;
use App\Models\Project;
use App\Models\Business;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Contracts\Auth\Authenticatable;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard.home'));
});

// Roles
Breadcrumbs::for('roles', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('User Roles', route('dashboard.roles'));
});

// Permissions
Breadcrumbs::for('permissions', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('User Permissions', route('dashboard.permissions'));
});

// System Settings
Breadcrumbs::for('system_settings', function (BreadcrumbTrail $trail) {
    $trail->parent('cms_dashboard');
    $trail->push('System Settings', route('cms.system-settings'));
});

// Businesses
Breadcrumbs::for('business_create', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Add Business', route('dashboard.businesses.create'));
});

Breadcrumbs::for('business_edit', function (BreadcrumbTrail $trail, Business $business) {
    $trail->parent('dashboard');
    $trail->push('Edit Business', route('dashboard.businesses.edit', $business->slug));
});

// Clients
Breadcrumbs::for('clients', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Clients', route('dashboard.clients.index'));
});

Breadcrumbs::for('clients_create', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Clients', route('dashboard.clients.index'));
    $trail->push('Add Client', route('dashboard.clients.create'));
});

Breadcrumbs::for('clients_edit', function (BreadcrumbTrail $trail, Client $client) {
    $trail->parent('dashboard');
    $trail->push('Clients', route('dashboard.clients.index'));
    $trail->push('Edit Client', route('dashboard.clients.edit', $client->slug));
});

// Projects
Breadcrumbs::for('projects', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Projects', route('dashboard.projects.index'));
});

Breadcrumbs::for('project_details', function (BreadcrumbTrail $trail, Project $project) {
    $trail->parent('dashboard');
    $trail->push('Projects', route('dashboard.projects.index'));
    $trail->push('Project Details', route('dashboard.projects.detail', $project->slug));
});

// Tasks
Breadcrumbs::for('tasks', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Tasks', route('dashboard.tasks.index'));
});

Breadcrumbs::for('task_details', function (BreadcrumbTrail $trail, Task $task) {
    $trail->parent('dashboard');
    $trail->push($task?->project?->name, route('dashboard.projects.detail', $task?->project?->slug));
    $trail->push($task?->name, route('dashboard.tasks.view', $task->id));
});

// Terms & Conditions
Breadcrumbs::for('terms_conditions', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Terms & Conditions', route('dashboard.terms-conditions.index'));
});

// Users
Breadcrumbs::for('users', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Users', route('dashboard.users.index'));
});

Breadcrumbs::for('users_profile', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('dashboard');
    $trail->push('Users', route('dashboard.users.index'));
    $trail->push('Users Profile', route('dashboard.users.profile', $user->id));
});

// Auth user profile
Breadcrumbs::for('user_profile', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('User Profile', route('dashboard.profile'));
});

Breadcrumbs::for('update_password', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Update Password', route('dashboard.update-password'));
});

Breadcrumbs::for('user_contracts', function (BreadcrumbTrail $trail, Authenticatable $user) {
    $trail->parent('dashboard');
    $trail->push('Profile', route('dashboard.users.profile', $user->id));
    $trail->push('My Contracts', route('dashboard.users.contracts'));
});

// Imvoices
Breadcrumbs::for('invoices', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Invoices', route('dashboard.invoices.index'));
});

// Knowledge base
Breadcrumbs::for('knowledge_base', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Knowledge Base', route('dashboard.knowledgebase.index'));
});

Breadcrumbs::for('knowledge_base_keyword', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Knowledge Base', route('dashboard.knowledgebase.index'));
    $trail->push('Search Knowledge Base');
});

// Email
Breadcrumbs::for('emails', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Emails', route('dashboard.emails'));
});

// CMS Dashboard
Breadcrumbs::for('cms_dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('cms.dashboard'));
});
// CMS Business
Breadcrumbs::for('Businesses', function (BreadcrumbTrail $trail) {
    $trail->parent('cms_dashboard');
    $trail->push('Businesses', route('cms.businesses'));
});
