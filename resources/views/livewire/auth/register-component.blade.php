<div>
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
            <input type="text" class="form-control @error('form.first_name') is-invalid @enderror" id="first_name" name="first_name" wire:model="form.first_name" placeholder="First Name"
                aria-describedby="first_name" tabindex="1" autofocus />
            @error('form.first_name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-1">
            <input type="text" class="form-control @error('form.last_name') is-invalid @enderror" id="last_name" name="last_name" wire:model="form.last_name" placeholder="Last Name"
                aria-describedby="last_name" tabindex="2" />
            @error('form.last_name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-1">
            <input type="text" class="form-control @error('form.phone') is-invalid @enderror" id="phone" name="phone" wire:model="form.phone" placeholder="Phone"
                aria-describedby="phone" tabindex="3" />
            @error('form.phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-1">
            <input type="email" class="form-control @error('form.email') is-invalid @enderror" id="email" name="email" wire:model="form.email" placeholder="Email"
                aria-describedby="email" tabindex="6" />
            @error('form.email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-1">
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge @error('form.password') is-invalid @enderror" wire:model="form.password" id="password"
                    name="password" tabindex="7" placeholder="Password" aria-describedby="password" />
                <span class="input-group-text cursor-pointer" wire:ignore><i data-feather="eye"></i></span>
            </div>
            @error('form.password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-1">
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control @error('form.password_confirmation') is-invalid @enderror" wire:model="form.password_confirmation" id="password_confirmation"
                name="password_confirmation" tabindex="8" placeholder="Confirm Password" aria-describedby="password_confirmation" />
                <span class="input-group-text cursor-pointer" wire:ignore><i data-feather="eye"></i></span>
            </div>
            @error('form.password_confirmation')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button class="btn btn-primary w-100 mt-2" tabindex="9" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Sign in') }}</span>
            <span wire:loading>
                <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
            </span>
        </button>
    </form>
</div>
