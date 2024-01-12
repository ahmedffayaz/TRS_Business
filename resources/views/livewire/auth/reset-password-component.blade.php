    <form class="auth-reset-password-form mt-2" wire:submit.prevent="submit">
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
        <input type="hidden" name="token" wire:model="token">
        <div class="mb-1">
            <div class="form-control-wrap">
                <input type="text" class="form-control @error('email') is-invalid @enderror" wire:model="email" id="email" name="email" placeholder="Email"
                    aria-describedby="email" tabindex="1" autofocus readonly />
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="mb-1">
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" wire:model="password" id="reset-password-new"
                    name="password" placeholder="New Password" aria-describedby="reset-password-new" tabindex="1" autofocus=""  />
                <span class="input-group-text cursor-pointer" wire:ignore><i data-feather="eye"></i></span>
            </div>
            @error('password')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-1">
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge @error('password_confirmation') is-invalid @enderror" wire:model="password_confirmation"
                    id="reset-password-confirm" name="password_confirmation" placeholder="Confirm Password" aria-describedby="reset-password-confirm" tabindex="2"
                     />
                <span class="input-group-text cursor-pointer" wire:ignore><i data-feather="eye"></i></span>
            </div>
        </div>
        @error('password_confirmation')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
        <button class="btn btn-primary w-100 mt-2" tabindex="4" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Set New Password') }}</span>
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
