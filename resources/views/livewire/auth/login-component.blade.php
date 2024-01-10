<form class="auth-login-form mt-2" wire:submit.prevent="login">
    @csrf
    @if (session()->has('status'))
        <div class="alert alert-success p-1" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            {{ session('status') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-error alert-danger p-1" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            {{ session('error') }}
        </div>
    @endif
    <div class="mb-1">
        <input type="text" class="form-control @error('form.email') is-invalid @enderror" id="email" name="email" wire:model="form.email" placeholder="Email"
            aria-describedby="email" tabindex="1" autofocus />
        @error('form.email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="mb-1">
        <div class="input-group input-group-merge form-password-toggle">
            <input type="password" class="form-control form-control-merge @error('form.password') is-invalid @enderror" wire:model="form.password" id="password"
                name="password" tabindex="2" placeholder="Password" aria-describedby="password" />
            <span class="input-group-text cursor-pointer" wire:ignore><i data-feather="eye"></i></span>
        </div>
        @error('form.password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="mb-1 d-flex justify-content-between">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember-me" wire:model="remember" name="remember" tabindex="3" />
            <label class="form-check-label" for="remember-me"> Remember Me </label>
        </div>
        <a href="{{ route('password.request') }}">
            <small>{{ __('Forgot Password?') }}</small>
        </a>
    </div>
    <button class="btn btn-primary w-100 mt-2" tabindex="4">{{ __('Sign in') }}</button>
</form>
