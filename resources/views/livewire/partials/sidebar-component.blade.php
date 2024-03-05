<div>
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto">
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <span class="brand-logo">
                            <img src="{{ $business->logo ? asset('storage/' . $business->logo) : asset('trs_logo.svg') }}" alt="" height="40">
                        </span>
                        <h2 class="brand-text">TRS/DEV CMS</h2>
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
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <x-nav class="{{ request()->routeIs('dashboard') ? 'active' : '' }} nav-item">
                    <x-anchor-tag class="d-flex align-items-center" href="{{ url('dashboard') }}">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span>
                    </x-anchor-tag>
                </x-nav>
                @can('view_clients')
                    <x-nav class="{{ request()->routeIs('dashboard.clients.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.clients.index') }}">
                            <i data-feather="user-check"></i>
                            <span class="menu-title text-truncate" data-i18n="Clients">Clients</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                <x-nav class="{{ request()->routeIs('dashboard.employees') ? 'active' : '' }} nav-item">
                    <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.employees') }}">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate" data-i18n="Companies">Employees</span>
                    </x-anchor-tag>
                </x-nav>
                <li class="{{ request()->routeIs('dashboard.projects') ? 'active' : '' }} nav-item">
                    <a class="d-flex align-items-center" href="{{ route('dashboard.projects') }}">
                        <i data-feather="file-text"></i>
                        <span class="menu-title text-truncate" data-i18n="Projects">Projects</span>
                    </a>
                </li>
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather="check-circle"></i>
                        <span class="menu-title text-truncate" data-i18n="Tasks">Tasks</span>
                    </a>
                </li>
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather="clipboard"></i>
                        <span class="menu-title text-truncate" data-i18n="Invoices">Invoices</span>
                    </a>
                </li>
                @can('view_terms_conditions')
                    <x-nav class="{{ request()->routeIs('dashboard.terms-conditions.index') ? 'active' : '' }} nav-item">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.terms-conditions.index') }}">
                            <i data-feather='copy'></i>
                            <span class="menu-title text-truncate" data-i18n="Knowledge Base">Terms & Conditions</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                @can('view_knowledgeBase')
                    <x-nav class="{{ request()->routeIs('dashboard.knowledge-bases.index') ? 'active' : '' }} nav-item">
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
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather="shield"></i>
                        <span class="menu-title text-truncate" data-i18n="Roles &amp; Permissions">Roles &amp; Permissions</span>
                    </a>
                    <ul class="menu-content">
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('dashboard.roles') }}">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate" data-i18n="Roles">Roles</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('dashboard.permissions') }}">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate" data-i18n="Permissions">Permission</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @can('edit_systems')
                    <x-nav class="nav-itme">
                        <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.system-setting') }}">
                            <i data-feather="settings"></i>
                            <span class="menu-title text-truncate" data-i18n="System Settings">System Settings</span>
                        </x-anchor-tag>
                    </x-nav>
                @endcan
                @can('edit_businesses')
                    <x-nav class="nav-itme">
                        <x-anchor-tag class="d-flex align-items-center" href="#">
                            <i data-feather='aperture'></i>
                            <span class="menu-title text-truncate" data-i18n="Business Settings">Business Settings</span>
                        </x-anchor-tag>
                        <ul class="menu-content">
                            <x-nav>
                                <x-anchor-tag class="d-flex align-items-center" href="{{ route('dashboard.businesses.edit', $business->slug) }}">
                                    <i data-feather="settings"></i>
                                    <span class="menu-title text-truncate" data-i18n="Settings">Edit Business</span>
                                </x-anchor-tag>
                            </x-nav>
                        </ul>
                    </x-nav>
                @endcan
            </ul>
        </div>
    </div>
</div>
