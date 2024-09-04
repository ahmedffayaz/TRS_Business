<style>
    .nav-toggle {
        margin-top: 8px;
    }

    .vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content>li>a i,
    .vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content>li>a svg {
        width: 15px;
        height: 15px;
    }

    .main-menu .navbar-header .navbar-brand .brand-logo img {
        max-width: 100%;
    }
</style>
<div>
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow expanded" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto">
                    <a class="navbar-brand" href="{{ route('dashboard.home') }}">
                        <span class="brand-logo">
                            <img class="logo-light" src="{{ cmsLogo() }}" alt="Light Mode Logo" height="40" >
                            <img class="logo-dark" src="{{ darkModeCMSLogo() }}" alt="Dark Mode Logo" height="40" style="display: none">
                            <img class="light-short-logo" src="{{ shortCMSLogo() }}" alt="Short CMS Logo" height="40" style="display: none">
                            <img class="dark-short-logo" src="{{ darkShortCMSLogo() }}" alt="Short CMS Logo" height="40" style="display: none">
                        </span>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                        <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content mt-3">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

                <li class="nav-item {{ request()->routeIs('dashboard.home*') ? 'has-sub sidebar-group-active open' : '' }}" style="">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate" data-i18n="Dashboards">Dashboards</span>
                        <span class="badge badge-light-warning rounded-pill ms-auto me-1">2</span>
                    </a>
                    <ul class="menu-content">
                        @can('view_analytics')
                        <li class="{{ request()->routeIs('dashboard.home') ? 'active' : '' }} nav-item">
                            <x-anchor-tag class="d-flex align-items-center" href="{{ url('dashboard') }}">
                                 <i data-feather="circle"></i>
                                <span class="menu-item text-truncate" data-i18n="Analytics">Analytics</span>
                            </x-anchor-tag>
                        </li>
                        @endcan

                        <li class="{{ request()->routeIs('dashboard.home.calendar') ? 'active' : '' }} nav-item">
                            <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.home.calendar') }}">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate" data-i18n="eCommerce">Calendar</span>
                            </x-anchor-tag>
                        </li>
                    </ul>
                </li>

                {{-- <x-nav class="{{ request()->routeIs('dashboard.home') ? 'active' : '' }} nav-item">
                    <x-anchor-tag class="d-flex align-items-center" href="{{ url('dashboard') }}">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span>
                    </x-anchor-tag>
                </x-nav> --}}
                @can('view_clients')
                    <x-nav class="{{ request()->routeIs('dashboard.clients.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.clients.index') }}">
                            <i data-feather="user-check"></i>
                            <span class="menu-title text-truncate" data-i18n="Clients">Clients</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                <li class=" nav-item">
                    @can('view_users')
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather="shield"></i>
                        <span class="menu-title text-truncate" data-i18n="Companies &amp; Permissions">Users</span>
                    </a>
                    @endcan
                    <ul class="menu-content">
                        <li>
                            @can('view_users')
                                <x-nav class="{{ request()->routeIs('dashboard.users.index') ? 'active' : '' }} nav-item">
                                    <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.users.index') }}">
                                        <i data-feather="users"></i>
                                        <span class="menu-title text-truncate" data-i18n="Companies">Users</span>
                                    </x-anchor-tag>
                                </x-nav>
                            @endcan
                        </li>
                        <li>
                            @can('view_leaves')
                                <x-nav class="{{ request()->routeIs('dashboard.leave.index') ? 'active' : '' }} nav-item">
                                    <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.leave.index') }}">
                                        <i data-feather="circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Companies">Leaves</span>
                                    </x-anchor-tag>
                                </x-nav>
                            @endcan
                        </li>
                    </ul>
                </li>
                @if (auth()->user()->can('view_projects') || auth()->user()->can('view_associated_projects'))
                    <x-nav class="{{ request()->routeIs('dashboard.projects.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.projects.index') }}">
                            <i data-feather="file-text"></i>
                            <span class="menu-title text-truncate" data-i18n="Projects">Projects</span>
                        </x-anchor-tag>
                    </x-nav>
                @endif
                @can('view_tasks')
                    <x-nav class="{{ request()->routeIs('dashboard.tasks.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.tasks.index') }}">
                            <i data-feather="check-circle"></i>
                            <span class="menu-title text-truncate" data-i18n="Tasks">Tasks</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                @can('view_invoices')
                    <x-nav class="{{ request()->routeIs('dashboard.invoices.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.invoices.index') }}">
                            <i data-feather="clipboard"></i>
                            <span class="menu-title text-truncate" data-i18n="Tasks">Invoices</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                @can('view_terms_conditions')
                    <x-nav class="{{ request()->routeIs('dashboard.terms-conditions.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.terms-conditions.index') }}">
                            <i data-feather='copy'></i>
                            <span class="menu-title text-truncate" data-i18n="Knowledge Base">Terms & Conditions</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                @can('view_knowledgeBase')
                    <x-nav class="{{ request()->routeIs('dashboard.knowledgebase.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.knowledgebase.index') }}">
                            <i data-feather='help-circle'></i>
                            <span class="menu-title text-truncate" data-i18n="Knowledge Base">Knowledge Base</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                <li class=" navigation-header">
                    <span data-i18n="System Settings">System Settings</span>
                    <i data-feather="more-horizontal"></i>
                </li>
                @can('view_roles')
                    <x-nav class="{{ request()->routeIs('dashboard.roles') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.roles') }}">
                            <i data-feather="shield"></i>
                            <span class="menu-item text-truncate" data-i18n="Roles">Roles</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                @can('edit_businesses')
                    <x-nav class="{{ request()->routeIs('dashboard.businesses.edit') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.businesses.edit', $business->slug) }}">
                            <i data-feather="settings"></i>
                            <span class="menu-title text-truncate" data-i18n="BusinessSettings">Business Settings</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan

                <li class="nav-item {{ request()->routeIs('dashboard.emails*') ? 'has-sub sidebar-group-active open' : '' }}">
                    @can('view_emails')
                        <a class="d-flex align-items-center" href="#">
                            <i data-feather="mail"></i>
                            <span class="menu-title text-truncate" data-i18n="Companies &amp; Permissions">Emails</span>
                        </a>
                    @endcan
                    <ul class="menu-content">
                        <li>
                            @can('view_emails')
                                <x-nav class="{{ request()->routeIs('dashboard.emails.settings') ? 'active' : '' }} nav-item">
                                    <x-anchor-tag class="d-flex align-items-center {{ request()->routeIs('dashboard.emails.settings') ? 'active' : '' }}"
                                        href="{{ route('dashboard.emails.settings') }}">
                                        <i data-feather="settings"></i>
                                        <span class="menu-title text-truncate" data-i18n="Companies">Email Settings</span>
                                    </x-anchor-tag>
                                </x-nav>
                            @endcan
                        </li>
                        <li>
                            @can('view_emails')
                                <x-nav class="{{ request()->routeIs('dashboard.emails') ? 'active' : '' }} nav-item">
                                    <x-anchor-tag class="d-flex align-items-center {{ request()->routeIs('dashboard.emails') ? 'active' : '' }}"
                                        href="{{ route('dashboard.emails') }}">
                                        <i data-feather="mail"></i>
                                        <span class="menu-title text-truncate" data-i18n="Emails">Email Templates</span>
                                    </x-anchor-tag>
                                </x-nav>
                            @endcan
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>

