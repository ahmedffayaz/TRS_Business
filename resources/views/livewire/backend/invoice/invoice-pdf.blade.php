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
        * {
            margin: 0;
        }

        span {
            font-size: 15px;
            color: #727272
        }

        p {
            font-size: 15px;
            color: #727272
        }

        tbody tr td td {
            color: #727272
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        hr {
            margin: 1rem 0;
            color: #ebe9f1;
            background-color: currentColor;
            border: 0;
            opacity: 1;
        }

        .py-1 {
            padding-top: 16px !important;
            padding-bottom: 16px !important;
        }

        .invoice-title {
            text-align: right;
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

        .logo {
            height: 70px;
        }

        .page-header {
            background-color: #F3F3FF;
        }

        .page-header td {
            line-height: 30px;
        }

        .logo-container {
            display: flex;
        }

        .header-content {
            padding: 15px 15px 15px 15px;
        }

        .header-content-fontsize {
            font-size: 17px;
        }

        .details td {
            padding-left: 10px;
        }

        .details-header {
            font-size: 17px;
            color: #03096D;
            margin-bottom: 10px;
        }

        .row-data td {
            padding-top: 19px;
            text-align: center
        }

        .details td p {
            line-height: 28px;
            font-size: 15px
        }

        .main-details {}

        td table tbody {
            line-height: 25px;
            font-size: 15px
        }

        .calculation {
            line-height: 25px;
        }
    </style>
</head>

<body>
    <div>
        <table width="100%" style="padding: 0; height:auto">
            <tbody>
                <tr class="page-header">
                    <td>
                        <div class="header-content">
                            <div class="logo-container">
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
                                <h3 style="margin-top:20px; color:#03096D">{{ $data?->project?->client?->business?->name }}</h3>
                            </div>
                            <p style="margin-top: 5px">{{ $data?->project?->client?->address }}</p>
                            <p style="margin-top: 1px">{{ $data?->project?->client?->city . ', ' . $data?->project?->client?->post_code }}</p>
                            <p style="margin-top: 1px">{{ $data?->project?->client?->country?->name }}</p>
                        </div>
                    </td>
                    <td style= "padding: 20px">
                        <div class="mb-25 text-end">
                            <h4><b style="color:#03096D; font-size: 19px">Invoice</b> <span class="invoice-date">{{ $data?->invoice_number }}</span></h4>
                        </div>
                        <div class="mb-25 text-end">
                            <p>Date Issued: <span class="invoice-date">{{ formatDate($data?->created_at) }}</span></p>
                        </div>
                        <div class="text-end">
                            <p class="">Due Date: <span class="invoice-due-date">{{ formatDate($data?->due_at) }}</p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <hr style="margin-top: 15px; margin-bottom: 15px; color: white; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="margin: 30px 0 50px  0px">
            <table class="main-details" style="width: 100%;">
                <tbody>
                    <tr class="details">
                        <td style="padding-left:20px">
                            <h6 class="details-header">Invoice To:</h6>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>{{ $data?->project?->client?->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $data?->project?->client?->address }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $data?->project?->client?->city . ', ' . $data?->project?->client?->postal_code }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $data?->project?->client?->country?->name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>

                        <td style="padding-left:40px;">
                            <h6 class="mb-2 fw-bold details-header">Project Details:</h6>
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="pe-1">Project Name:</td>
                                        <td><span class="fw-bold">{{ ucwords($data?->project?->name) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Project Type:</td>
                                        <td>{{ ucwords($data?->project?->type->value) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Project Status:</td>
                                        <td>{{ ucwords($data?->project?->status->value) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Project Members:</td>
                                        <td>{{ str_pad($data?->project?->members->count(), 2, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <h6 class="mb-2 fw-bold details-header">Payment Details:</h6>
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
        </div>
        <table class="table mt-50" width="100%"
            style="caption-side: bottom; border-collapse: collapse; padding:0; margin:0;
        margin-bottom: 1rem; color: dark-grey; vertical-align: middle; border-color: #E5E5E5; margin-bottom: 0;
        border-bottom-left-radius: 0.357rem; border-bottom-right-radius: 0.357rem; background-color: transparent; border-style:none">
            <thead style="vertical-align: top; text-transform: uppercase; font-size: 0.857rem; border-color: white;">
                <tr style="border-color: #E5E5E5; border: 1px solid #E5E5E5">
                    <th class="py-1" style="font-size: 12px; border-color: #E5E5E5;  background-color: white; color: #03096D;">Task description</th>
                    <th class="py-1" style="font-size: 12px; border-color: #E5E5E5;  background-color: white; color: #03096D;">Rate</th>
                    <th class="py-1" style="font-size: 12px; border-color: #E5E5E5;  background-color: white; color: #03096D;">Hours</th>
                    <th class="py-1" style="font-size: 12px; border-color: #E5E5E5;  background-color: white; color: #03096D;">Total</th>
                </tr>
            </thead>
            <tbody style="vertical-align: bottom;">
                @foreach ($data->invoiceData as $record)
                    <tr class="row-data">
                        <td style="">
                            <p class="fw-bold mb-25 text-nowrap">{{ $record->task ? optional($record->task)->name : $record->comments }}</p>
                        </td>
                        <td style="margin-right: 5px; padding-right: 10px;">
                            <p class="fw-bold mb-25 text-nowrap">{{ $record->rate_per_hour ? formatCurrency($record->rate_per_hour, $data->currency) : '-' }}</p>
                        </td>
                        <td style="margin-right: 5px; padding-right: 10px;">
                            <p class="fw-bold mb-25 text-nowrap">{{ $record?->task ? formatTime($record->time) : round($record->time / 60, 2) }}</p>
                        </td>
                        <td style="margin-right: 5px; padding-right: 10px;">
                            <p class="fw-bold mb-25 text-nowrap">{{ $record->amount ? formatCurrency($record->amount, $data->currency) : '-' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <hr style="margin-top: 1%; margin-bottom: 15px; color: #E5E5E5; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="width: 200px; position:static; right: 0; margin: 40px 45px 0 600px; text-align:end" class="calculation">
        <p>Subtotal: <span class="fw-bold" style="margin-left: 45px;">{{ formatCurrency($subTotal, $data->currency) }}</span></p>
        <p>Adjusted: <span class="fw-bold" style="margin-left: 42px;">{{ formatCurrency($data->deduction ?? 0, $data->currency) }}</span></p>

        <hr style="margin-top: 10px; margin-bottom: 15px; color: #E5E5E5; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />

        <p style="color:#03096D">Total Cost: <span class="fw-bold" style="margin-left: 27px; color: #03096D"> {{ formatCurrency($total, $data->currency) }}</span></p>
    </div>

    <div>
        @if ($data->notes)
            <footer
                style="
        padding: 15px;
        font-size: 14px;
        color: #727272;
        position: fixed;
        border: 1px solid #E5E5E5;
        bottom: 0;
        left: 0;
        width: 100%;
        box-sizing: border-box;
        margin-bottom: 20px;">
                <div>Note</div>
                <span colspan="8">
                    <hr style="margin-top: 1%; margin-bottom: 15px; color: #E5E5E5; background-color: currentColor; border: 0; height: 1px; opacity: 1;" />
                </span>
                <p>{!! $data->notes !!}</p>
            </footer>
        @endif
    </div>

</body>

</html>
