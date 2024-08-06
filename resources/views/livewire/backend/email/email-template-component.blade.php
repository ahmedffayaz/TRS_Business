<div>
    @section('breadcrumbs', Breadcrumbs::render('emails_template'))

    <div class="accordion accordion-margin" id="accordionMargin">
        <div class="row">
            @if (isset($emails))
                @foreach ($emails as $email)
                    <div class="col-md-6 mb-2">
                        <h2>{{ $email?->title }}</h2>
                        <p>{{ $email?->detail }}</p>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingMarginOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#accordion-email-{{ $email?->id }}" aria-expanded="false"
                                    aria-controls="accordion-email-{{ $email?->id }}">
                                    {{ $email?->subject }}
                                </button>
                            </h2>
                            <div id="accordion-email-{{ $email?->id }}" class="accordion-collapse collapse"
                                aria-labelledby="headingMarginOne" data-bs-parent="#accordionMargin">
                                <div class="accordion-body">
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-md-6">Subject: {{ $email?->subject }}</div>
                                        <div class="col-md-6"><x-anchor-tag href="javascript:void(0);" class="btn btn-primary float-end" wire:click="edit({{ $email?->id }})" value="Edit" /></div>
                                    </div>
                                    <div role="article" aria-roledescription="email" aria-label="" lang="en" style=" font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;">
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
                                                            <td align="center" class="sm-px-24" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;">{!! $email?->body !!}</td>
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
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <x-main-modal wireIgnoreSelf="wire:ignore.self" modalSize="modal-xl" modalTitle="Edit Email Template">
        @include('livewire.backend.email.form')
    </x-main-modal>
</div>
