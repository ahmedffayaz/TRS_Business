@if ($form->isUpdate)
    <form wire:submit.prevent="update({{ $form?->id }})">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12 mb-1">
                        <x-input-label for="subject" value="Subject" />
                        <x-input id="subject" :class="$errors->has('form.subject') ? 'error' : ''" wire:model="form.subject" />
                        @error('invoiceForm.all_comments')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-12 mb-1">
                        <x-input-label for="allowed-tag" value="Allowed Tags" />
                        <x-textarea rows="2" id="allowed-tag" wire:model="form.tags" disabled />
                    </div>
                    <div class="col-md-12 mb-1">
                        <x-input-label for="email-body" value="Email Template" />
                        <x-textarea name="body" id="email-body" rows="17" wire:model="form.body"
                            :class="$errors->has('form.body') ? 'error' : ''" />
                    </div>
                    <div class="col-md-12 text-center">
                        <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $form->isUpdate ? 'Update' : 'Add' }}</span>
                            <x-button-loader />
                        </x-button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <x-input-label for="email-layout" value="Email Layout" />
                <div>
                    <table style="width: 100%; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td align="center" style=" mso-line-height-rule: exactly; background-color: #eceff1; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;">
                                <table class="sm-w-full" style="width: 600px" cellpadding="0"
                                    cellspacing="0" role="presentation">
                                    <tr>
                                        <td class="sm-py-32 sm-px-24" style=" mso-line-height-rule: exactly; padding: 48px; text-align: center; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;">
                                            <a href="https://1.envato.market/vuexy_admin"
                                                style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;">
                                                <img src="{{ getSiteLogo($business->logo) }}" width="auto" height="80" alt="Vuexy Admin" style=" max-width: 100%; vertical-align: middle; line-height: 100%; border: 0;" />
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" class="sm-px-24" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;" id="email-template">{!! $form?->body !!}</td>
                                    </tr>
                                    <tr>
                                        <td style=" font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; height: 20px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="mso-line-height-rule: exactly; padding-left: 48px; padding-right: 48px; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 14px; color: #eceff1;">
                                            <p align="center" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; margin-bottom: 16px; cursor: default;">
                                                <a href="https://www.facebook.com/pixinvents" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; color: #263238; text-decoration: none;">
                                                    <img src="{{ asset('assets/images/facebook.png') }}" width="17" alt="Facebook" style=" max-width: 100%; vertical-align: middle; line-height: 100%; border: 0; margin-right: 12px;" /></a>
                                                &bull;
                                                <a href="https://twitter.com/pixinvents" style=" font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; color: #263238; text-decoration: none;">
                                                    <img src="{{ asset('assets/images/twitter.png') }}" width="17" alt="Twitter" style="max-width: 100%; vertical-align: middle; line-height: 100%; border: 0; margin-right: 12px;" /></a>
                                                &bull;
                                                <a href="https://www.instagram.com/pixinvents" style=" font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; color: #263238; text-decoration: none;">
                                                    <img src="{{ asset('assets/images/instagram.png') }}" width="17" alt="Instagram" style=" max-width: 100%; vertical-align: middle; line-height: 100%; border: 0; margin-right: 12px;" /></a>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly; height: 16px;">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </form>
@endif

@script
    <script type="module">
        $(document).ready(function () {
            $(document).on('keyup change', '#email-body', function (event) {
                let $emailBody = $('#email-body').val();
                $('#email-template').html($emailBody);
            });
        });
    </script>
@endscript
