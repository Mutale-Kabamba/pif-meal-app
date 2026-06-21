<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Beneficiary Cards - {{ $project->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; }
        .page { width: 210mm; min-height: 297mm; padding: 10mm; }
        .grid { display: flex; flex-wrap: wrap; gap: 8mm; }
        .card {
            width: 90mm;
            height: 65mm;
            border: 2px solid #333;
            border-radius: 4mm;
            padding: 5mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-inside: avoid;
        }
        .card-header {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2mm;
        }
        .card-name {
            font-size: 14pt;
            font-weight: bold;
            color: #111;
            margin-bottom: 3mm;
        }
        .card-code {
            font-size: 22pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #059669;
            letter-spacing: 3px;
            margin: 3mm 0;
        }
        .qr-code { margin-top: 2mm; }
        .qr-code svg { width: 28mm; height: 28mm; }
        .footer {
            position: fixed;
            bottom: 5mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }
        @media print {
            .page { page-break-after: always; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div style="text-align: center; margin-bottom: 8mm;">
            <h1 style="font-size: 16pt;">{{ $project->name }}</h1>
            <p style="font-size: 10pt; color: #666;">Budget Code: {{ $project->budget_code }}</p>
            <p style="font-size: 9pt; color: #999; margin-top: 2mm;">Generated: {{ now()->format('F j, Y') }}</p>
        </div>

        <div class="grid">
            @foreach ($beneficiaries as $beneficiary)
                <div class="card">
                    <div class="card-header">Nutrition Program</div>
                    <div class="card-name">{{ $beneficiary->name }}</div>
                    <div class="card-code">{{ $beneficiary->shortcode }}</div>
                    <div class="qr-code">
                        {!! QrCode::size(100)->generate($beneficiary->qr_token) !!}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="footer">
            Nutrition Monitoring System | {{ $project->name }} | {{ $beneficiaries->count() }} cards
        </div>
    </div>
</body>
</html>
