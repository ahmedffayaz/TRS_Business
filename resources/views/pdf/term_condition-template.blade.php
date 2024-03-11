<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            margin-top: 3cm;
            margin-left: 5px;
            margin-right: 5px;
            margin-bottom: 10px;
            font-family: sourcesanspro, sans-serif !important;
        }

        header {
            border-bottom: 1px solid black;
            position: fixed;
            top: 20px;
            left: 0cm;
            right: 0cm;
            height: 2.2cm;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 40px;
            border-top: 1px solid black;
        }

        footer img {
            margin-left: 0px;
        }

        @page {
            size: 25cm landscape;
        }

        header table {
            border-bottom: 0px solid white;
            border-collapse: collapse;
            width: 100%;
        }

        header table tr,
        th,
        td {
            border: 1px solid rgb(255, 255, 255) border-collapse: collapse;
        }

        .font-12 {
            font-size: 12px;
        }

        label {
            font-size: 12px;
        }

        .steps-table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        .steps-table tr th {
            font-size: 11.7px;
            font-weight: bold;
            background: #C8CFE2;
        }

        .steps-table tr td {
            font-size: 10px;
            text-align: center;
            padding: 4px;
        }

        .job-safety-equipement {
            text-align: center;
            width: 100%;
        }

        .job-safety-equipement td {
            border: none;
        }

        .extra-info {
            text-align: center;
            width: 100%;
        }

        .extra-info td {
            border: none;
        }

        .custom-control {
            position: relative;
            z-index: 1;
            min-height: 1.5rem;
            padding-left: 1.5rem;
        }

        .custom-control-label {
            position: relative;
            margin-bottom: 0;
            vertical-align: top;
        }

        .p-text {
            text-align: left;
            margin-top: 0;
            margin-bottom: 0;
            padding-left: 3px;
            border-radius: 3px;
        }
    </style>
    <title>Document</title>
</head>

<body>
    {{-- header --}}
    <header>
        <div style="margin-right: 20px; margin-left: 30px;">
            <table>
                <tr>
                    <td style="border: none">
                        <img alt="" src='data:image/png;base64,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'
                            style="margin-left: 10px; float:left; margin-top: 12px; margin-right: 2px; height: 50px;" />
                    </td>
                </tr>
            </table>
        </div>
    </header>

    {{-- footer --}}
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2023 A Project by <a class="ms-25" href="#" target="_blank">The Right Software</a><span class="d-none d-sm-inline-block"><i data-feather="heart"></i></span></p>
    </footer>
    {{-- <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button> --}}

    <main>
        <div style="margin-right: 15px; margin-left: 25px;">

            <div style="line-height: 1.6; margin-bottom:50px;">
                <div style="margin-left: 50px; margin-left: 20px;text-transform:capitalize;">
                    <h2 style=" text-align:center; font-weight:bold;">
                        {!! $termsCondition->title . '[' . $termsCondition->version . ']' !!}</h2>
                    {!! $termsCondition->description !!}
                    <div style="margin-top: 30px;  text-align: right;" >
                        <h5>
                            {!! $name !!}
                        </h5>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>

</html>
