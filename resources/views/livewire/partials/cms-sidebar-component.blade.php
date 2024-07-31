<div>
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto">
                    <a class="navbar-brand" href="{{ route('dashboard.home') }}">
                        <span class="brand-logo">
                            <img src="{{ cmsLogo() }}" alt="" height="40">
                        </span>
                        <h2 class="brand-text">{{ strlen(cmsName()) > 10
                            ? substr(cmsName(), 0, 10) . '...'
                            : cmsName() }}</h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                        <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary"
                            data-feather="disc" data-ticon="disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <x-nav class="{{ request()->routeIs('dashboard.home') ? 'active' : '' }} nav-item">
                    <x-anchor-tag class="d-flex align-items-center" href="{{ url('dashboard') }}">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span>
                    </x-anchor-tag>
                </x-nav>
                @can('manage_system_settings')
                <x-nav class="{{ request()->routeIs('cms.system-settings') ? 'active' : '' }} nav-item">
                    <x-anchor-tag class="d-flex align-items-center" href="{{ route('cms.system-settings') }}">
                        <i class="me-50" data-feather="settings"></i>
                        <span class="menu-title text-truncate" data-i18n="Dashboard">Settings</span>
                    </x-anchor-tag>
                </x-nav>
                @endcan
                @can('view_businesses')
                <x-nav class="{{ request()->routeIs('cms.businesses') ? 'active' : '' }} nav-item">
                    <x-anchor-tag class="d-flex align-items-center" href="{{ route('cms.businesses') }}">
                        <i class="me-50" data-feather="list"></i>
                        <span class="menu-title text-truncate" data-i18n="Dashboard">Business</span>
                    </x-anchor-tag>
                </x-nav>
                @endcan
                <li class=" navigation-header">
                    <span data-i18n="System Settings">System Settings</span>
                    <i data-feather="more-horizontal"></i>
                </li>
            </ul>
        </div>
    </div>
</div>
