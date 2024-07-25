<div>
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                @if (auth()->check() && !request()->routeIs('select-business') && !request()->routeIs('dashboard.terms-conditions.accept'))
                    @if (count($businesses) > 0)
                        <x-nav class="nav-item header-select2">
                            <x-select-input wire:model="businessId" wire:change="getBusinessState" id="businessId">
                                @foreach ($businesses as $key => $values)
                                    <option value="{{ $key }}">{{ $values }}</option>
                                @endforeach
                            </x-select-input>
                        </x-nav>
                    @endif
                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a></li>
                <li class="nav-item dropdown dropdown-notification me-25">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown">
                        <i class="ficon" data-feather="bell"></i>
                        <span class="badge rounded-pill bg-danger badge-up">5</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
                        <li class="dropdown-menu-header">
                            <div class="dropdown-header d-flex">
                                <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                                <div class="badge rounded-pill badge-light-primary">6 New</div>
                            </div>
                        </li>
                        <li class="scrollable-container media-list"><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar"><img src="{{ url('/assets/images/avatar.png') }}" alt="avatar" width="32" height="32">
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">Congratulation Sam 🎉</span>winner!</p><small class="notification-text"> Won the
                                            monthly best seller badge.</small>
                                    </div>
                                </div>
                            </a>
                            <a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar"><img src="{{ url('/assets/images/avatar.png') }}" alt="avatar" width="32" height="32">
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">New message</span>&nbsp;received</p><small class="notification-text"> You have 10
                                            unread messages</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar bg-light-danger">
                                            <div class="avatar-content">MD</div>
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">Revised Order 👋</span>&nbsp;checkout</p><small class="notification-text"> MD Inc.
                                            order updated</small>
                                    </div>
                                </div>
                            </a>
                            <div class="list-item d-flex align-items-center">
                                <h6 class="fw-bolder me-auto mb-0">System Notifications</h6>
                                <div class="form-check form-check-primary form-switch">
                                    <input class="form-check-input" id="systemNotification" type="checkbox" checked="">
                                    <label class="form-check-label" for="systemNotification"></label>
                                </div>
                            </div>
                            <a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar bg-light-danger">
                                            <div class="avatar-content"><i class="avatar-icon" data-feather="x"></i></div>
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">Server down</span>&nbsp;registered</p><small class="notification-text"> USA Server is
                                            down due to high CPU usage</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="dropdown-menu-footer"><a class="btn btn-primary w-100" href="#">Read all notifications</a></li>
                    </ul>
                </li>
                @endif
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @php
                            $avatar = auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : '/assets/images/avatar.png';
                        @endphp
                        <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder">{{ ucfirst(auth()->user()->first_name) }}
                                {{ ucfirst(auth()->user()->last_name) }}</span><span class="user-status">{{ ucwords(getAuthRoles()) }}</span></div><span class="avatar"><img
                                class="round" src="{{ url($avatar) }}" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user" style="width : 15rem;">
                        @if (auth()->check() && !request()->routeIs('select-business') && !request()->routeIs('dashboard.terms-conditions.accept'))
                            <x-anchor-tag class="dropdown-item" href="{{ route('dashboard.profile') }}">
                                <i class="me-50" data-feather="user"></i> Profile
                            </x-anchor-tag>
                            <x-anchor-tag class="dropdown-item" href="{{ route('dashboard.update-password') }}">
                                <i class="me-50" data-feather="key"></i> Change Password
                            </x-anchor-tag>
                            @can('view_systems')
                                <x-anchor-tag class="dropdown-item"
                                    href="{{ route('dashboard.system-setting') }}">
                                    <i data-feather="settings"></i>
                                    <span class="menu-title text-truncate" data-i18n="Settings">System Settings</span>
                                </x-anchor-tag>
                            @endcan
                            <div class="dropdown-divider"></div>
                            @can('add_businesses')
                                <x-anchor-tag class="dropdown-item" href="{{ route('dashboard.businesses.create') }}">
                                    <i class="me-50" data-feather="edit"></i> Add Business
                                </x-anchor-tag>
                            @endcan
                            @if (auth()->user()->can('edit_business') && $business)
                                <x-anchor-tag class="dropdown-item" href="{{ route('dashboard.businesses.edit', $business->slug) }}">
                                    <i class="me-50" data-feather="settings"></i> Settings
                                </x-anchor-tag>
                            @endif
                            @can('view_contracts')
                                <x-anchor-tag class="dropdown-item" href="{{ route('dashboard.users.contracts') }}">
                                    <i class="me-50" data-feather="copy"></i> My Contracts
                                </x-anchor-tag>
                            @endcan
                        @endif
                        <form class="border-0 m-0 p-0" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item w-100"><i class="me-50" data-feather="power"></i> Logout</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

</div>

