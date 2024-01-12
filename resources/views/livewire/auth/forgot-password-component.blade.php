    <form class="auth-login-form mt-2" wire:submit.prevent="submit">
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
            <input type="text" class="form-control  @error('email') is-invalid @enderror" id="email" name="email" wire:model="email" placeholder="Email"
                aria-describedby="email" tabindex="1" autofocus />
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button class="btn btn-primary w-100 mt-2" tabindex="4" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Send Reset Link') }}</span>
            <span wire:loading>
                <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
            </span>
        </button>
        <p class="text-center mt-2">
            <a href="{{ route('login') }}" wire:ignore>
                <i data-feather="chevron-left"></i>
                Back to login
            </a>
        </p>
    </form>
