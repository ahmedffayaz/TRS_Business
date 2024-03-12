<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use App\Models\User;
use App\Models\Client;
// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use App\Models\Business;
use Diglactic\Breadcrumbs\Breadcrumbs;
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
    $trail->parent('dashboard');
    $trail->push('System Settings', route('dashboard.system-setting'));
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
    $trail->push('Projects', route('dashboard.projects'));
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

Breadcrumbs::for('user_profile', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('User Profile', route('dashboard.profile'));
});

Breadcrumbs::for('update_password', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Update Password', route('dashboard.update-password'));
});

Breadcrumbs::for('user_contracts', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Profile', route('user-profile'));
    $trail->push('My Contracts', route('dashboard.users.contracts'));
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
