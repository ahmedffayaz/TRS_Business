@php
    $invoiceData = getInvoiceRecord($data['invoice_id']);
    $subTotal = $invoiceData->total;
    $total = $subTotal + $invoiceData->deduction;
    // Format upto 2 decimal places
    $subTotal = number_format((float) $subTotal, 2, '.', '');
    $total = number_format((float) $total, 2, '.', '');
    $cols = 3;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed,
        figure, figcaption, footer, header, hgroup,
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 13px;
            font: inherit;
            vertical-align: baseline;
        }
        /* HTML5 display-role reset for older browsers */
        article, aside, details, figcaption, figure,
        footer, header, hgroup, menu, nav, section {
            display: block;
        }
        body {
            line-height: 1;
            margin: 20px;
            font-family: 'Montserrat', Helvetica, Arial, serif !important;
            font-size: 13px !important;
            font-weight: 400 !important;
            background-color: #fff;
            color: #5e5873 !important;
        }
        span {
            font-family: 'Montserrat', Helvetica, Arial, serif !important;
        }
        p {
            font-family: 'Montserrat', Helvetica, Arial, serif !important;
        }
        th {
            font-family: 'Montserrat', Helvetica, Arial, serif !important;
        }
        td {
            font-family: 'Montserrat', Helvetica, Arial, serif !important;
        }
        ol, ul {
            list-style: none;
        }
        blockquote, q {
            quotes: none;
        }
        blockquote:before, blockquote:after,
        q:before, q:after {
            content: '';
            content: none;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 500 !important;
            font-size: 13px !important;
        }

        hr {
            margin: 1rem 0;
            color: #ebe9f1;
            background-color: currentColor;
            border: 0;
            opacity: 1;
        }

        hr:not([size]) {
            height: 1px;
        }

        .container {
            margin: 5px;
        }

        .mb-25 {
            margin-bottom: 4px !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-2 {
            margin-bottom: 24px !important;
        }

        .mt-50 {
            margin-top: 50px !important;
        }

        .pe-1 {
            padding-right: 16px !important;
        }

        .py-1 {
            padding-top: 16px !important;
            padding-bottom: 16px !important;
        }

        .text-nowrap {
            white-space: nowrap !important;
        }

        .logo-wrapper {
            margin-bottom: 1.9rem;
        }

        .invoice-title {
            text-align: right;
            margin-bottom: 3rem;
        }

        .invoice-number {
            font-weight: 600 !important;
        }

        .text-end {
            text-align: right !important;
        }

        .invoice-date {
            font-weight: 600 !important;
            margin-left: 4px;
        }

        .invoice-due-date {
            font-weight: 600 !important;
            margin-left: 15px;
        }

        .invoice-spacing {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .fw-bold {
            font-weight: bold !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <table width="100%">
            <tbody>
                <tr>
                    <td>
                        <div class="logo-wrapper">
                            @php
                                if (!empty($invoiceData?->project?->client?->business?->logo)) {
                                    $src = '';
                                    if (Storage::disk('public')->exists($invoiceData?->project?->client?->business?->logo)) {
                                        $logo = explode('.', $invoiceData?->project?->client?->business?->logo);
                                        $ext = end($logo);
                                        $baseEnCodeImage = base64_encode(Storage::disk('public')->get($invoiceData?->project?->client?->business?->logo));
                                        $src = "data:image/{$ext};base64,{$baseEnCodeImage}";
                                        echo '<img class="logo" src="' . $src . '" alt=""/>';
                                    }
                                }
                            @endphp
                        </div>
                        <p class="mb-25">{{ $invoiceData?->project?->client?->address }}</p>
                        <p class="mb-25">{{ $invoiceData?->project?->client?->city . ', ' . $invoiceData?->project?->client?->post_code }}</p>
                        <p class="mb-0">{{ $invoiceData?->project?->client?->country?->name }}</p>
                    </td>
                    <td>
                        <h4 class="invoice-title">
                            Invoice <span class="invoice-number">{{ $invoiceData?->invoice_number }}</span>
                        </h4>
                        <div class="mb-25 text-end">
                            <p class="invoice-date-title">Date Issued: <span class="invoice-date">{{ formatDate($issueDate) }}</span></p>
                        </div>
                        <div class="text-end">
                            <p class="invoice-date-title">Billed At: <span class="invoice-due-date">{{ formatDate($invoiceData?->due_at) }}</p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <hr style="margin-top: 15px; margin-bottom: 15px; color: #ebe9f1; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 20px; padding-bottom: 20px;">Payment Receipt</td>
                </tr>

                <tr>
                    <td colspan="2">
                        <table>
                            <tbody>
                                <tr>
                                    <th style="width: 180px; text-align: left;">Payment Date:</th>
                                    <td style="width: 300px;">{{$data['billed_at']}}</td>
                                    <td style="background-color: #90ee90; width: 150px;">Amount</td>
                                </tr>
                                <tr>
                                    <th style="width: 180px; text-align: left;">Refrence Number:</th>
                                    <td style="width: 300px;"></td>
                                    <td style="background-color: #90ee90">Received</td>
                                </tr>
                                <tr>
                                    <th style="width: 180px; text-align: left;">Payment mode:</th>
                                    <td style="width: 300px;">{{$data['bank']}}</td>
                                    <td style="background-color: #90ee90; width: 150px;">{{ formatCurrency($data['amount'], $invoiceData->currency) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <hr style="margin-top: 15px; margin-bottom: 15px; color: #ebe9f1; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <h6 class="mb-2 fw-bold">Bill To:</h6>
                        <h6 class="mb-25 fw-bold">{{ $invoiceData?->project?->client?->name }}</h6>
                        <p class="card-text mb-25">{{ $invoiceData?->project?->client?->address }}</p>
                        <p class="card-text mb-25">{{ $invoiceData?->project?->client?->city . ', ' . $invoiceData?->project?->client?->postal_code }}</p>
                        <p class="card-text mb-25">{{ $invoiceData?->project?->client?->country?->name }}</p>
                        <p class="card-text mb-0"></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table mt-50" style="caption-side: bottom; border-collapse: collapse; width: 100%;
        margin-bottom: 1rem; color: #6e6b7b; vertical-align: middle; border-color: #ebe9f1; margin-bottom: 0;
        border-bottom-left-radius: 0.357rem; border-bottom-right-radius: 0.357rem; background-color: transparent; ">
            <thead style="vertical-align: top; text-transform: uppercase; font-size: 0.857rem; letter-spacing: 0.5px; border-color: #6e6b7b; border-style: solid; border-width: 2;">
                <tr style="border-color: #6e6b7b; border-style: solid; border-width: 2;">
                    <th class="py-1" style="width: 420px; margin-right: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Invoice Number</th>
                    <th class="py-1" style="margin-right: width: 100px; 5px; margin-left: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Invoice Date</th>
                    <th class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Amount Invoice</th>
                    <th class="py-1" style="width: 100px; margin-left: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Payment Amount</th>
                </tr>
            </thead>
            <tbody style="vertical-align: bottom;">
                <tr>
                    <td class="py-1" style="width: 420px; margin-right: 5px; padding-right: 10px;">
                        <p class="fw-bold mb-25 text-nowrap">{{ $invoiceData->invoice_number }}</p>
                    </td>
                    <td class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; text-align: center;">
                        <span class="fw-bold">{{ formatDate($invoiceData->created_at) }}</span>
                    </td>
                    <td class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; text-align: center;">
                        <span class="fw-bold">{{ formatCurrency($total, $invoiceData->currency) }}</span>
                    </td>
                    <td class="py-1" style="width: 100px; margin-left: 5px; text-align: center;">
                        <span class="fw-bold">{{formatCurrency($data['amount'], $invoiceData->currency )}}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
