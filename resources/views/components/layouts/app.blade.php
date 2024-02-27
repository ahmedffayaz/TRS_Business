<!DOCTYPE html>
<html lang="en" class="loading">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="author" content="TRS" />
    <title>{{ $title ?? 'Page Title' }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet" />
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        let _business_logo = (e) => e.src = `{{ asset('assets/images/company-default-logo.png') }}`;
    </script>
    @livewireStyles
</head>

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">
    @livewire('partials.header-component')
    @if (auth()->check() && !request()->routeIs('select-business'))
        @livewire('partials.sidebar-component')
    @endif
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                @livewire('partials.breadcrumbs-component')
                @livewire('partials.breadcrumbs-button-component')
            </div>
            <div class="content-body">
                {{ $slot }}
            </div>
        </div>
    </div>
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
    @include('partials.footer')
    @yield('modals')
    @livewireScripts
    <script>
        Livewire.on('updatedBreadcrumbs', data => {
            Livewire.emit('updateBreadcrumbs', data.breadcrumbs);
        });
    </script>
</body>

</html>
