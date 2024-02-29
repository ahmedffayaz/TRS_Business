<?php

use Illuminate\Http\Request;
use WireUi\Breadcrumbs\Breadcrumbs;
use WireUi\Breadcrumbs\Trail;

use function PHPUnit\Framework\callback;

Breadcrumbs::for('dashboard')
    ->push('Dashboard')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Dashboard');
    });

Breadcrumbs::for('dashboard.roles')
    ->push('Dashboard', route('dashboard'))
    ->push('User Roles')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('User Roles');
    });

Breadcrumbs::for('dashboard.permissions')
    ->push('Dashboard', route('dashboard'))
    ->push('User Permissions')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('User Permissions');
    });

Breadcrumbs::for('dashboard.system-setting')
    ->push('Dashboard', route('dashboard'))
    ->push('System Settings')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('System Settings');
    });

Breadcrumbs::for('dashboard.businesses.create')
    ->push('Dashboard', route('dashboard'))
    ->push('Add Business')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Add Business');
    });

Breadcrumbs::for('dashboard.businesses.edit')
    ->push('Dashboard', route('dashboard'))
    ->push('Edit Business')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Edit Business');
    });

Breadcrumbs::for('dashboard.clients.index')
    ->push('Dashboard', route('dashboard'))
    ->push('Clients')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Clients');
    });

Breadcrumbs::for('dashboard.clients.create')
    ->push('Dashboard', route('dashboard'))
    ->push('Clients', route('dashboard.clients.index'))
    ->push('Add Client')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Add Client');
    });

Breadcrumbs::for('dashboard.clients.edit')
    ->push('Dashboard', route('dashboard'))
    ->push('Clients', route('dashboard.clients.index'))
    ->push('Edit Client')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Edit Client');
    });

Breadcrumbs::for('dashboard.employees')
    ->push('Dashboard', route('dashboard'))
    ->push('Employees')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Employees');
    });

Breadcrumbs::for('dashboard.projects')
    ->push('Dashboard', route('dashboard'))
    ->push('Projects')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Projects');
    });

Breadcrumbs::for('dashboard.user-contracts')
    ->push('Dashboard', route('dashboard'))
    ->push('User Terms & Conditions')
    ->callback(function (Trail $trail, Request $request): Trail {
        return $trail->push('Terms & Conditions');
    });

Breadcrumbs::for('dashboard.knowledgebase.index')
    ->push('Dashboard', route('dashboard'))
    ->push('Knowledge Base')
    ->callback(function (Trail $trail, Request $request) : Trail {
        return $trail->push('Knowledge Base');
    });

Breadcrumbs::for('dashboard.knowledgebase.search-keyword')
    ->push('Dashboard', route('dashboard'))
    ->push('Knowledge Base', route('dashboard.knowledgebase.index'))
    ->push('Search Knowledge Base')
    ->callback(function (Trail $trail, Request $request) : Trail {
        return $trail->push('Search Knowledge Base');
    });
