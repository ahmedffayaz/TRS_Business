<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-basic px-2">
                    <div class="auth-inner my-2">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="index.html" class="brand-logo">
                                    <img src="{{ asset('trs_logo.svg') }}" alt="" height="60">
                                </a>
                                <h4 class="card-title mb-1 text-center">The Right Software</h4>
                                @if (session('status'))
                                    <div class="alert alert-success p-1" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @elseif (session('errror'))
                                    <div class="alert alert-danger p-1" role="alert">
                                        {{ session('errror') }}
                                    </div>
                                @endif
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    setTimeout(function() {
        var alertElement = document.querySelector('.alert');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 2000);
</script>

</html>
