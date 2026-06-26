<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Beneficiary Cards - {{ $project->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; }

        @page {
            size: 210mm 297mm;
            margin: 6mm;
        }

        /* Outer card grid */
        /* border-collapse merges adjacent borders - single line between cards */
        .cards-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .cards-table td.card-cell {
            width: 50%;
            height: 57mm;
            border: 0.75pt solid #374151;
            padding: 0;
            vertical-align: middle;
        }

        /* Inner card layout (text | QR) */
        .card-inner {
            width: 100%;
            height: 57mm;
            border-collapse: collapse;
        }
        .text-cell {
            vertical-align: middle;
            text-align: left;
            padding: 3mm 2mm 3mm 4mm;
        }
        .qr-cell {
            width: 38mm;
            vertical-align: middle;
            text-align: center;
            padding: 2mm 2.5mm;
        }

        /* Card text content */
        .card-sublabel {
            font-size: 6pt;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1mm;
        }
        .card-title {
            font-size: 18pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2mm;
        }
        .card-name {
            font-size: 12pt;
            font-weight: bold;
            color: #374151;
            margin-bottom: 0.5mm;
        }
        .card-code {
            font-size: 15pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #059669;
            letter-spacing: 2px;
            margin-bottom: 1.5mm;
        }
        .card-project {
            font-size: 8pt;
            color: #6b7280;
        }

        /* Literacy badge */
        .literacy-badge {
            display: inline-block;
            font-size: 6pt;
            font-weight: bold;
            color: #1d4ed8;
            background: #dbeafe;
            border: 0.5pt solid #93c5fd;
            border-radius: 2pt;
            padding: 0.5mm 1.5mm;
            margin-top: 1.5mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* QR image */
        .qr-cell img {
            width: 30mm;
            height: 30mm;
            display: block;
            margin: 0 auto;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
@php $chunks = $beneficiaries->chunk(10); @endphp

@foreach ($chunks as $pageIndex => $pageCards)
    <div class="{{ $pageIndex > 0 ? 'page-break' : '' }}">
        <table class="cards-table">
            @foreach ($pageCards->chunk(2) as $row)
                <tr>
                    @foreach ($row as $beneficiary)
                        @php
                            $qrSvg = base64_encode(
                                QrCode::size(90)->margin(1)->generate($beneficiary->qr_token)
                            );
                        @endphp
                        <td class="card-cell">
                            <table class="card-inner">
                                <tr>
                                    <td class="text-cell">
                                        <div class="card-sublabel">Nutrition Program</div>
                                        <div class="card-title">Meal Card</div>
                                        <div class="card-name">{{ $beneficiary->name }}</div>
                                        <div class="card-code">{{ $beneficiary->shortcode }}</div>
                                        <div class="card-project">{{ $project->name }}</div>
                                        @if($beneficiary->literacy_enrolled)
                                            <div class="literacy-badge">Literacy</div>
                                        @endif
                                    </td>
                                    <td class="qr-cell">
                                        <img src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="QR">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    @endforeach
                    @if ($row->count() === 1)
                        <td class="card-cell"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
@endforeach
</body>
</html>
