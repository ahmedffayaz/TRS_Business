<x-guest-layout>
    <form class="auth-login-form mt-2" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-1">
            <input type="text" class="form-control  @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" aria-describedby="email" tabindex="1" autofocus required />
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button class="btn btn-primary w-100 mt-2" tabindex="4">Send Reset Link</button>
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
