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
            margin-top: 2.7cm;
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
            background-color: #FBFBFB;
            border-bottom: 1px solid #cecece !important;
            padding: 8px;
            text-align: left;
            color: #03096D;
            font-size: 11px;

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
            $src = ($business_details['logo'] != null && Storage::disk('public')->exists($business_details['logo'])) ?  'storage/' . $business_details['logo'] : null;
        @endphp

        <table
            style="width: 91%; border-collapse: collapse; margin-top: 12px; position: absolute;margin-left: 0.7cm;margin-right: 1cm;">
            <tr>
                <td style="width: 33%; vertical-align: middle; text-align: left;">
                    @if($src)
                        <img class="logo" src="{{ $src }}" alt="Business Logo" style="max-width: 170px; height: 40px;" width="170" height="45" />
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle; width: 33%;">

                </td>
                <td style="text-align: right; vertical-align: middle; width: 33%;">
                    <h2 style="color: #03096D; margin: 0;">Knowledge Base</h2>
                    <span style="font-size: 11px;">Generated at: {{ \Carbon\Carbon::now()->format('d M, Y') }}</span>
                </td>
            </tr>
        </table>
    </header>




    <main class="container">
        @foreach ($categoryBaseQuestionsAnswers as $categoryName => $knowledgeBase)
            <div style="margin-bottom: 10px; color:#03096D;  margin-top: 30px;">
                <h5 style="">{{ ucwords($categoryName) ?? 'null' }}</h5>
            </div>
            <div style="overflow: hidden; margin-bottom: 20px; border: 0.2px solid #dcdcdc;border-radius: 3px;">
                @foreach ($knowledgeBase as $questionAnswer)
                    <div style="padding: 10px; font-size: 12px; border-bottom: 0.2px solid #dcdcdc">
                       <div style="margin-bottom: 7px;"> {{ $questionAnswer->question }} </div>
                    <dive
                        style="font-size: 11px; color: #7F7F7F;">
                        {{ $questionAnswer->answer }}
                    </dive>
                    </div>

                @endforeach
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
