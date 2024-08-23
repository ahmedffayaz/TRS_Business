@section('breadcrumbs', Breadcrumbs::render('emails_settings'))
<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Email settings</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="alert-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="showSettings" wire:model="showForm"
                    wire:change="toggleForm">
                <label class="form-check-label" for="showSettings">Use system email configurations</label>
            </div>

            @unless ($showForm)
                <form wire:submit.prevent="submit">
                    <div class="mt-2">
                        <div class="row row mb-1">
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="email_driver" value="Email Driver:" />
                                    <x-select-input id="email_driver" name="email_driver" wire:model="email_driver"
                                        :class="$errors->has('email_driver') ? 'error' : ''">
                                        <option value="smtp">SMTP</option>
                                        <option value="mailgun">Mailgun</option>
                                    </x-select-input>
                                    @error('email_driver')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="host" value="Host:" />
                                    <x-input id="host" name="host" type="text" wire:model="host"
                                        :class="$errors->has('host') ? 'error' : ''" />
                                    @error('host')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row row mb-1">
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="port" value="Port:" />
                                    <x-input id="port" name="port" type="text" wire:model="port"
                                        :class="$errors->has('port') ? 'error' : ''" />
                                    @error('port')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="username" value="User Name:" />
                                    <x-input id="username" name="username" type="text" wire:model="username"
                                        :class="$errors->has('username') ? 'error' : ''" />
                                    @error('username')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row row mb-1">
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="password" value="Password:" />
                                    <x-input id="password" name="password" type="password" wire:model="password"
                                        :class="$errors->has('password') ? 'error' : ''" />
                                    @error('password')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="encryption" value="Encryption:" />
                                    <x-input id="encryption" name="encryption" type="text" wire:model="encryption"
                                        :class="$errors->has('encryption') ? 'error' : ''" />
                                    @error('encryption')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row row mb-1">
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="from_address" value="From Address:" />
                                    <x-input id="from_address" name="from_address" type="text" wire:model="from_address"
                                        :class="$errors->has('from_address') ? 'error' : ''" />
                                    @error('from_address')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <x-input-label for="from_name" value="From Name:" />
                                    <x-input id="from_name" name="from_name" type="text" wire:model="from_name"
                                        :class="$errors->has('from_name') ? 'error' : ''" />
                                    @error('from_name')
                                        <x-input-error :message="$message" />
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endunless
        </div>

    </div>
</div>

@script
    <script type="module">
        $(document).ready(function() {
            // Handle change event on checkboxes
            $('input[name="setting_type"]').change(function() {
                if ($('#custom_setting').is(':checked')) {
                    $('#custom_settings').show();
                } else {
                    $('#custom_settings').hide();
                }

                if ($('#defaultSetting').is(':checked')) {
                    $('#default_settings').show();
                } else {
                    $('#default_settings').hide();
                }
            });

            // Trigger change event on page load
            $('input[name="setting_type"]:checked').trigger('change');
        });
    </script>
@endscript
