<x-guest-layout>
    <form class="auth-login-form mt-2" action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-1">
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" aria-describedby="email" tabindex="1" autofocus />
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-1">
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" id="password" name="password" tabindex="2" placeholder="Password"
                    aria-describedby="password" />
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
</x-guest-layout>
