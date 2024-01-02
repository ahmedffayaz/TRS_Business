<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto"><a class="navbar-brand" href="../../../html/ltr/vertical-menu-template/index.html"><span class="brand-logo">
                        <img src="{{ asset('trs_logo.svg') }}" alt="" height="40"></span>
                    <h2 class="brand-text">TRS/DEV CMS</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('dashboard') }}"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span></a>
            </li>
            <li class="{{ request()->routeIs('employees') ? 'open' : '' }} nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="users"></i><span class="menu-title text-truncate" data-i18n="Users">Employees</span></a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('employees') ? 'active' : '' }}"><a class="d-flex align-items-center" href="{{ route('employees') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">List</span></a>
                    </li>
                    <li><a class="d-flex align-items-center" href="#"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="View">View</span></a>
                    </li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('companies') ? 'active' : '' }} nav-item"><a class="d-flex align-items-center" href="{{ route('companies') }}"><i data-feather="package"></i><span class="menu-title text-truncate" data-i18n="Companies">Companies</span></a>
            </li>
            <li class="{{ request()->routeIs('clients') ? 'active' : '' }} nav-item"><a class="d-flex align-items-center" href="{{ route('clients') }}"><i data-feather="user-check"></i><span class="menu-title text-truncate" data-i18n="Clients">Clients</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('projects') }}"><i data-feather="file-text"></i><span class="menu-title text-truncate" data-i18n="Projects">Projects</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="check-circle"></i><span class="menu-title text-truncate" data-i18n="Tasks">Tasks</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="clipboard"></i><span class="menu-title text-truncate" data-i18n="Invoices">Invoices</span></a>
            </li>

            <li class=" navigation-header"><span data-i18n="System Settings">System Settings</span><i data-feather="more-horizontal"></i>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="shield"></i><span class="menu-title text-truncate" data-i18n="Roles &amp; Permissions">Roles &amp; Permissions</span></a>
                <ul class="menu-content">
                    <li><a class="d-flex align-items-center" href="{{ route('roles') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Roles">Roles</span></a>
                    </li>
                    <li><a class="d-flex align-items-center" href="#"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Permissions">Permission</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="settings"></i><span class="menu-title text-truncate" data-i18n="Settings">Settings</span></a>
            </li>
        </ul>
    </div>
</div>
