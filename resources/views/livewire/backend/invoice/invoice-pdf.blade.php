@php
    $subTotal = $data->total;
    $total = $subTotal + $data->deduction;
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
                                if (!empty($data?->project?->client?->business?->logo)) {
                                    $src = '';
                                    if (Storage::disk('public')->exists($data?->project?->client?->business?->logo)) {
                                        $logo = explode('.', $data?->project?->client?->business?->logo);
                                        $ext = end($logo);
                                        $baseEnCodeImage = base64_encode(Storage::disk('public')->get($data?->project?->client?->business?->logo));
                                        $src = "data:image/{$ext};base64,{$baseEnCodeImage}";
                                        echo '<img class="logo" src="' . $src . '" alt=""/>';
                                    }
                                }
                            @endphp
                        </div>
                        <p class="mb-25">{{ $data?->project?->client?->address }}</p>
                        <p class="mb-25">{{ $data?->project?->client?->city . ', ' . $data?->project?->client?->post_code }}</p>
                        <p class="mb-0">{{ $data?->project?->client?->country?->name }}</p>
                    </td>
                    <td>
                        <h4 class="invoice-title">
                            Invoice <span class="invoice-number">{{ $data?->invoice_number }}</span>
                        </h4>
                        <div class="mb-25 text-end">
                            <p class="invoice-date-title">Date Issued: <span class="invoice-date">{{ formatDate($data?->created_at) }}</span></p>
                        </div>
                        <div class="text-end">
                            <p class="invoice-date-title">Due Date: <span class="invoice-due-date">{{ formatDate($data?->due_at) }}</p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <hr style="margin-top: 15px; margin-bottom: 15px; color: #ebe9f1; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                    </td>
                </tr>

                <tr>
                    <td width="70%">
                        <h6 class="mb-2 fw-bold">Invoice To:</h6>
                        <h6 class="mb-25 fw-bold">{{ $data?->project?->client?->name }}</h6>
                        <p class="card-text mb-25">{{ $data?->project?->client?->address }}</p>
                        <p class="card-text mb-25">{{ $data?->project?->client?->city . ', ' . $data?->project?->client?->postal_code }}</p>
                        <p class="card-text mb-25">{{ $data?->project?->client?->country?->name }}</p>
                        <p class="card-text mb-0"></p>
                    </td>
                    <td width="30%">
                        <h6 class="mb-2 fw-bold">Payment Details:</h6>
                        <table>
                            <tbody>
                                <tr>
                                    <td class="pe-1">Total Due:</td>
                                    <td><span class="fw-bold">{{ formatCurrency($total, $data->currency) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Invoice Date:</td>
                                    <td>{{ formatDate($data->created_at) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Terms:</td>
                                    <td>Due on Receipt</td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Due Date:</td>
                                    <td>{{ formatDate($data->due_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table mt-50" style="caption-side: bottom; border-collapse: collapse; width: 100%;
        margin-bottom: 1rem; color: #6e6b7b; vertical-align: middle; border-color: #ebe9f1; margin-bottom: 0;
        border-bottom-left-radius: 0.357rem; border-bottom-right-radius: 0.357rem; background-color: transparent; ">
            <thead style="vertical-align: top; text-transform: uppercase; font-size: 0.857rem; letter-spacing: 0.5px; border-color: #6e6b7b; border-style: solid; border-width: 2;">
                <tr style="border-color: #6e6b7b; border-style: solid; border-width: 2;">
                    <th class="py-1" style="width: 420px; margin-right: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Task description</th>
                    <th class="py-1" style="margin-right: width: 100px; 5px; margin-left: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Rate</th>
                    <th class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Hours</th>
                    <th class="py-1" style="width: 100px; margin-left: 5px; font-size: 12px; border-color: #6e6b7b; border-style: solid; border-width: 2; background-color: #6e6b7b; color: #fff;">Total</th>
                </tr>
            </thead>
            <tbody style="vertical-align: bottom;">
                @foreach ($data->invoiceData as $record)
                    <tr style=";">
                        <td class="py-1" style="width: 420px; margin-right: 5px; padding-right: 10px;">
                            <p class="fw-bold mb-25 text-nowrap">{{ $record->task ? optional($record->task)->name : $record->comments }}</p>
                        </td>
                        <td class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; text-align: center;">
                            <span class="fw-bold">{{ $record->rate_per_hour ? formatCurrency($record->rate_per_hour, $data->currency) : '-' }}</span>
                        </td>
                        <td class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; text-align: center;">
                            <span class="fw-bold">{{ $record?->task ? formatTime($record->time) : round($record->time / 60, 2) }}</span>
                        </td>
                        <td class="py-1" style="width: 100px; margin-left: 5px; text-align: center;">
                            <span class="fw-bold">{{ $record->amount ? formatCurrency($record->amount, $data->currency) : '-' }}</span>
                        </td>
                    </tr>

                    @if ($record?->task)
                        @foreach ($record->task->comments as $key => $comment)
                            <tr style=";">
                                <td colspan="4" style=";">
                                    <hr style="margin-top: 15px; margin-bottom: 15px; color: #ebe9f1; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                                </td>
                            </tr>
                            <tr style=";">
                                <td class="py-1" style="width: 420px; margin-right: 5px; padding-right: 10px;">
                                    <span class="fw-bold">{!! $comment->description !!}</span>
                                </td>
                                <td class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; text-align: center;">
                                    <span class="fw-bold">{{ $record->amount ? formatCurrency($record->amount, $data->currency) : '-' }}</span>
                                </td>
                                <td class="py-1" style="width: 100px; margin-right: 5px; margin-left: 5px; text-align: center;">
                                    <span class="fw-bold">{{ formatTime($comment->time) }}</span>
                                </td>
                                <td class="py-1" colspan="2"style="width: 100px; margin-left: 5px; text-align: center;">
                                </td>
                            </tr>
                            @if ($loop->last)
                                <tr>
                                    <td colspan="4">
                                        <hr style="margin-top: 15px; margin-bottom: 15px; color: #ebe9f1; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <tr>
                    <td style="width: 420px; margin-right: 5px;"></td>
                    <td style="width: 100px; margin-right: 5px;"></td>
                    <td colspan="2" style="width: 200px;">
                        <p>Subtotal: <span class="fw-bold" style="margin-left: 45px;">{{ formatCurrency($subTotal, $data->currency) }}</span></p>
                        <p>Adjusted: <span class="fw-bold" style="margin-left: 42px;">{{ formatCurrency($data->deduction ?? 0, $data->currency) }}</span></p>
                        <p>Total: <span class="fw-bold" style="margin-left: 58px;">{{ formatCurrency($total, $data->currency) }}</span></p>

                        <hr style="margin-top: 15px; margin-bottom: 15px; color: #ebe9f1; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />

                        <p>Balance Due: <span class="fw-bold" style="margin-left: 27px;">{{ formatCurrency($total, $data->currency) }}</span></p>
                    </td>
                </tr>

                @if (!empty($data->notes))
                    <tr>
                        <td colspan="4">
                            <hr style="margin-top: 15px; margin-bottom: 15px; color: #ebe9f1; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <span class="fw-bold">Notes: </span>
                            <span>{!! $data->notes !!}</span>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>
