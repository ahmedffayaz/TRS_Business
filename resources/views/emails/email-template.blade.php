<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
        <meta charset="utf-8" />
        <meta name="x-apple-disable-message-reformatting" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="format-detection" content="telephone=no, date=no, address=no, email=no" />
        <link href="https://fonts.googleapis.com/css?family=Montserrat:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700" rel="stylesheet" media="screen" />
        <style>
            .hover-underline:hover {
                text-decoration: underline !important;
            }

            @media (max-width: 600px) {
                .sm-w-full {
                    width: 100% !important;
                }
                .sm-px-24 {
                    padding-left: 24px !important;
                    padding-right: 24px !important;
                }
                .sm-py-32 {
                    padding-top: 32px !important;
                    padding-bottom: 32px !important;
                }
            }
        </style>
    </head>
    <body style="margin: 0; width: 100%; padding: 0; word-break: break-word; -webkit-font-smoothing: antialiased;">
        <div role="article" aria-roledescription="email" aria-label="" lang="en" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;">
            <table style="width: 100%; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td align="center" style=" mso-line-height-rule: exactly; background-color: #eceff1; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;">
                        <table class="sm-w-full" style="width: 600px" cellpadding="0"
                            cellspacing="0" role="presentation">
                            <tr>
                                <td class="sm-py-32 sm-px-24" style=" mso-line-height-rule: exactly; padding: 48px; text-align: center; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;">
                                    <a href="https://1.envato.market/vuexy_admin"
                                        style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;">
                                        <img src="{{ getSiteLogo($businessLogo) }}" width="auto" height="80" alt="Vuexy Admin" style=" max-width: 100%; vertical-align: middle; line-height: 100%; border: 0;" />
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" class="sm-px-24" style="font-family: 'Montserrat', sans-serif; mso-line-height-rule: exactly;" id="email-template">{!! $emailMessage !!}</td>
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
    </body>
</html>
