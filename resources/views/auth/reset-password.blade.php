<x-guest-layout>
    <form class="auth-reset-password-form mt-2" method="POST" action="{{ route('password.store') }}" novalidate="novalidate">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-1">
            <div class="form-control-wrap">
                <input type="text" class="form-control @error('email') is-invalid @enderror" value="{{ $request->email }}" id="email" name="email" placeholder="Email" aria-describedby="email" tabindex="1" autofocus required />
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="mb-1">
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" id="reset-password-new" name="password" placeholder="New Password"
                    aria-describedby="reset-password-new" tabindex="1" autofocus="" required />
                <span class="input-group-text cursor-pointer ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </span>
            </div>
            @error('password')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-1">
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge @error('password_confirmation') is-invalid @enderror" id="reset-password-confirm" name="password_confirmation" placeholder="Confirm Password"
                    aria-describedby="reset-password-confirm" tabindex="2" required />
                <span class="input-group-text cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </span>
            </div>
        </div>
        @error('password_confirmation')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
        <button class="btn btn-primary w-100 waves-effect waves-float waves-light" tabindex="3">Set New Password</button>
    </form>
    <p class="text-center mt-2">
        <a href="{{ route('login') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Back to login
        </a>
    </p>
</x-guest-layout>
