<x-guest-layout>
    <div class="auth-inner my-2">
        <!-- Login basic -->
        <div class="card mb-0">
            <div class="card-body">
                <a href="index.html" class="brand-logo">
                    <img src="{{ asset('trs_logo.svg') }}" alt="" height="60">
                </a>

                <h4 class="card-title mb-1 text-center">Sign In</h4>

                <form class="auth-login-form mt-2" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-1">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                            aria-describedby="email" tabindex="1" autofocus />
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <div class="input-group input-group-merge form-password-toggle">
                            <input type="password" class="form-control form-control-merge" id="password"
                                name="password" tabindex="2" placeholder="Password" aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                        </div>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-1 d-flex justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember-me" name="remember" tabindex="3" />
                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                        </div>
                        <a href="{{ route('password.request') }}">
                            <small>{{ __('Forgot Password?') }}</small>
                        </a>
                    </div>
                    <button class="btn btn-primary w-100 mt-2" tabindex="4">{{ __('Sign in') }}</button>
                </form>

            </div>
        </div>
        <!-- /Login basic -->
    </div>
</x-guest-layout>
