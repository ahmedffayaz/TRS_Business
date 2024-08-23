<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task PDF</title>
    <style>
        * {
            padding: 0px;
            margin: 0px;
        }

        @page {
            margin: 0cm 0cm;
        }

        body {
            margin-top: 2cm;
            margin-left: 0.7cm;
            margin-right: 0.7cm;
            margin-bottom: .7cm;
            font-family: Arial, sans-serif;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: .7cm;
        }


        .header,
        .footer {
            background-color: #F3F3FF;
            padding: 5px;
            color: #7F7F7F
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            / margin-bottom: 20px; /
            font-size: 11px;
            font-weight: normal;
        }

        .table td {
            padding: 8px;
            text-align: left;
            color: #7F7F7F;
        }

        .table tr {
            border-bottom: 1px solid #cecece;
        }

        .table th {
            background-color: #f2f2f236;
            border-bottom: 1px solid #cecece !important;
            padding: 8px;
            text-align: left;
            color: #03096D;
            font-weight: medium;

        }

        .table tr:first-child {
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
        }

        .table tr:last-child {
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
            border-bottom-style: none;
        }
        .break-page {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <header class="header"
        style="width: 100%; max-width: 100%; display: flex; justify-content: center; align-items: center;">
        @php
            $business_details = session('business_details');
            $src = 'storage/' . $business_details['logo'];
        @endphp

        <table
            style="width: 91%; border-collapse: collapse; margin-top: 12px; position: absolute;margin-left: 0.7cm;margin-right: 1cm;">
            <tr>
                <td style="width: 33%; vertical-align: middle; text-align: left">
                    @if (!empty($src))
                        <img class="logo" src="{{ $src }}" alt=""
                            style="max-width: 200px; height: 50px;" />
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle; width: 33%;">

                </td>
                <td style="text-align: right; vertical-align: middle; width: 33%;">
                    <h2 style="color: #03096D; margin: 0;">Task Report</h2>
                    <span style="font-size: 11px;">Generated at: {{ \Carbon\Carbon::now()->format('d M, Y') }}</span>
                </td>
            </tr>
        </table>
    </header>




    <main class="container">
        @foreach ($groupedTasks as $projectName => $tasks)
            <div style="margin-bottom: 10px; color:#03096D;  margin-top: 30px;">
                <h5 style="">{{ ucwords($projectName) ?? 'null' }}</h5>
            </div>
            <div style="border-radius: 3px; overflow: hidden; border: .5px solid #cecece; margin-bottom: 20px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Task Description</th>
                            <th style="width: 50%;">Rate</th>
                            <th style="width: 25%;">Time spent</th>
                            <th style="width: 25%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->invoiceData as $record)
                        <tr style=";">
                            <td>{{ $record->task ? optional($record->task)->name : $record->comments }}</td>
                            <td>{{ $record->rate_per_hour ? formatCurrency($record->rate_per_hour, $data->currency) : '-' }}</td>
                            <td>{{ $record?->task ? formatTime($record->time) : round($record->time / 60, 2) }}</td>
                            <td>{{ $record->amount ? formatCurrency($record->amount, $data->currency) : '-' }}</td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No Task Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (!$loop->last)
                <div class="break-page"></div>
            @endif
        @endforeach
    </main>

    <footer class="footer" style="width: 100%; padding: 10px 20px; position: fixed; bottom: 0;">
        <table style="width: 95%; border-collapse: collapse; margin-top: 8px; font-size: 10px ">
            <tr>
                <td style="text-align: left; vertical-align: middle;">
                    <p style="margin: 0;">{{ ucwords($business_details['address']) }}</p>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <p style="margin: 0;">© {{ \Carbon\Carbon::now()->format('Y') }}
                        {{ ucwords($business_details['name']) }}</p>
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>
