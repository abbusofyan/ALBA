<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            html {
                font-family: sans-serif;
                line-height: 1.15;
                margin: 0;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
                font-size: 10px;
                margin: 36pt;
            }

            h4 {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }

            p {
                margin-top: 0;
                margin-bottom: 1rem;
            }

            strong {
                font-weight: bolder;
            }

            img {
                vertical-align: middle;
                border-style: none;
            }

            table {
                border-collapse: collapse;
            }

            th {
                text-align: inherit;
            }

            h4, .h4 {
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }

            h4, .h4 {
                font-size: 1.5rem;
            }

            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #212529;
            }

            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
            }

            .table.table-items td {
                border-top: 1px solid #dee2e6;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid #dee2e6;
            }

            .mt-5 {
                margin-top: 3rem !important;
            }

            .pr-0,
            .px-0 {
                padding-right: 0 !important;
            }

            .pl-0,
            .px-0 {
                padding-left: 0 !important;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .text-uppercase {
                text-transform: uppercase !important;
            }
            * {
                font-family: "DejaVu Sans";
            }
            body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
                line-height: 1.1;
            }
            .party-header {
                font-size: 1.5rem;
                font-weight: 400;
            }
            .total-amount {
                font-size: 12px;
                font-weight: 700;
            }
            .border-0 {
                border: none !important;
            }
            .cool-gray {
                color: #6B7280;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
        </style>
    </head>

    <body>
        {{-- Header --}}
        <div style="float: right">
            @if($invoice->logo)
                <img src="{{ $invoice->getLogo() }}" alt="logo" height="100">
            @endif
        </div>

        <span class="text-uppercase" style="font-size: 30px;">
            <strong>JOB NUMBER</strong>
        </span>
        <br>
        <div style="padding-left: 50px">
            <span style="color: #0E4681; font-size: 15px;">
                <strong>Jobson Asia Pte Ltd</strong>
            </span>
        </div>

        <hr>

        <span style="padding-left: 50px; padding-right: 50px; font-size: 12px;">Gul Circle 156A</span>
        <span style="padding-left: 50px; padding-right: 50px; font-size: 12px;">SINGAPORE</span>
        <span style="padding-left: 50px; padding-right: 50px; font-size: 12px;">629614</span>

        <hr>

        <span><strong style="text-align: center;">Nr./Date</strong></span>
        <input type="text" style="height: 11px">
        <input type="text" style="height: 11px">

        <table>
            <thead>
              <tr>
                <th rowspan="1"><strong>M/V:</strong></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td rowspan="1"><strong>Reference:</strong></td>
                <td></td>
              </tr>
              <tr>
                <td rowspan="1"><strong>Place of Completion:</strong></td>
                <td></td>
              </tr>
              <tr>
                <td rowspan="1"><strong>Nr. of Technicians</strong></td>
                <td></td>
              </tr>
              <tr>
                <td rowspan="1"><strong>Name of Technicians:</strong></td>
                <td></td>
              </tr>
              <tr>
                <td rowspan="1"><strong>.</strong></td>
                <td></td>
              </tr>
            </tbody>
        </table>

        <table style="padding-top: 30px;">
            <thead>
              <tr>
                <th rowspan="1"><strong>Day</strong></th>
                <th rowspan="1"><strong>Date</strong></th>
                <th rowspan="1"><strong>Time from / to</strong></th>
                <th rowspan="1"><strong>tr</strong></th>
                <th rowspan="1"><strong>km</strong></th>
                <th rowspan="1"><strong>wo</strong></th>
                <th rowspan="1"><strong>wt</strong></th>
                <th rowspan="1"><strong>Job Description</strong></th>
              </tr>
            </thead>
        </table>

        <table style="padding-top: 5px;">
            <thead>
              <tr>
                <td style="height: 30px; width: 34px;"></td>
                <td style="height: 30px; width: 34px;"></td>
                <td style="width: 120px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 30px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 130px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
            </thead>
        </table>

        <table>
            <thead>
              <tr>
                <td style="height: 30px; width: 34px;"></td>
                <td style="height: 30px; width: 34px;"></td>
                <td style="width: 120px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 30px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 130px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
            </thead>
        </table>

        <table>
            <thead>
              <tr>
                <td style="height: 30px; width: 34px;"></td>
                <td style="height: 30px; width: 34px;"></td>
                <td style="width: 120px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 30px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 130px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
            </thead>
        </table>

        <table>
            <thead>
              <tr>
                <td style="height: 30px; width: 34px;"></td>
                <td style="height: 30px; width: 34px;"></td>
                <td style="width: 120px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 30px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 130px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
            </thead>
        </table>

        <table>
            <thead>
              <tr>
                <td style="height: 30px; width: 34px;"></td>
                <td style="height: 30px; width: 34px;"></td>
                <td style="width: 120px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 30px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 130px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
            </thead>
        </table>

        <table>
            <thead>
              <tr>
                <td style="height: 30px; width: 34px;"></td>
                <td style="height: 30px; width: 34px;"></td>
                <td style="width: 120px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 30px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 130px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
            </thead>
        </table>

        <table>
            <thead>
              <tr>
                <td style="height: 30px; width: 34px;"></td>
                <td style="height: 30px; width: 34px;"></td>
                <td style="width: 120px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 30px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 43px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td style="width: 130px; padding: 0px">
                    <table style="padding: 0px">
                        <thead>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">-</td>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
            </thead>
        </table>

        <div style="padding-top: 10px;"></div>

        <hr>

        <span><strong>Use of material see seperate list</strong></span>
        <br>
        <span>Service was rendered according to our actual valid conditions with respect to the provision of service personnel.</span>
        <br>
        <span>That the above made indication are correct and proper inspection of services rendered has been made is confirmaed by:</span>

        <hr>

        <span style="padding-left: 30px; padding-right: 230px; font-size: 8px; font-weight: bold;">Date</span>
        <span style="padding-right: 200px; font-size: 8px; font-weight: bold;">Service engineer</span>
        <span style="padding-right: 80px; font-size: 8px; font-weight: bold;">Customer</span>

        <div style="page-break-before: always;">
            <div style="float: right">
                @if($invoice->logo)
                    <img src="{{ $invoice->getLogo() }}" alt="logo" height="100">
                @endif
            </div>

            <span class="text-uppercase" style="font-size: 30px;">
                <strong>JOB NUMBER</strong>
            </span>
            <br>
            <div style="padding-left: 50px">
                <span style="color: #0E4681; font-size: 15px;">
                    <strong>Jobson Asia Pte Ltd</strong>
                </span>
            </div>
        <hr>

        <span style="padding-left: 50px; padding-right: 50px; font-size: 12px;">Gul Circle 156A</span>
        <span style="padding-left: 50px; padding-right: 50px; font-size: 12px;">SINGAPORE</span>
        <span style="padding-left: 50px; padding-right: 50px; font-size: 12px;">629614</span>

        <hr>

        <span><strong style="text-align: center;">Nr./Date</strong></span>
        <input type="text" style="height: 11px">
        <input type="text" style="height: 11px">

        <table style="padding-top: 5px;">
            <thead>
              <tr>
                <th rowspan="1"><strong>Totals</strong></th>
                <th rowspan="1"><strong>tr.:</strong></th>
                <th rowspan="1"><strong>wo:</strong></th>
                <th rowspan="1"><strong>wt:</strong></th>
                <th rowspan="1"><strong>km:</strong></th>
              </tr>
            </thead>
        </table>

        <table style="padding-top: 5px;">
            <thead>
              <tr>
                <th><strong>Remarks: <span style="padding-left: 10px;">tr = travel time</span> <span style="padding-left: 10px;">wo = working time</span> <span style="padding-left: 10px;">wt = waiting time</span></strong></th>
              </tr>
            </thead>
        </table>

        <hr>

        <span><strong>Use of material see seperate list</strong></span>
        <br>
        <span>Service was rendered according to our actual valid conditions with respect to the provision of service personnel.</span>
        <br>
        <span>That the above made indication are correct and proper inspection of services rendered has been made is confirmaed by:</span>

        <hr>

        <span style="padding-left: 30px; padding-right: 230px; font-size: 8px; font-weight: bold;">Date</span>
        <span style="padding-right: 200px; font-size: 8px; font-weight: bold;">Service engineer</span>
        <span style="padding-right: 80px; font-size: 8px; font-weight: bold;">Customer</span>

        </div>

        <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>
