<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meal Distribution Register - {{ $project ? $project->name : 'Play It Forward' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 6mm 7mm 6mm 7mm;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 7.5px;
            color: #1e293b;
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }

        /* Top Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .logo-text {
            font-size: 14px;
            font-weight: bold;
            color: #047857;
            letter-spacing: -0.5px;
        }
        .header-subtitle {
            font-size: 7.5px;
            font-weight: bold;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 1px;
            border-bottom: 2px solid #d97706;
            padding-bottom: 2px;
            display: inline-block;
        }
        .doc-title {
            text-align: right;
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .doc-meta {
            text-align: right;
            font-size: 6.5px;
            color: #64748b;
            margin-top: 1px;
        }

        /* Metadata Info Box */
        .info-box {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            background-color: #fffbeb;
            margin-bottom: 4px;
        }
        .info-box td {
            padding: 3px 6px;
            font-size: 7.5px;
            border-right: 1px solid #fde68a;
        }
        .info-box td:last-child {
            border-right: none;
        }
        .info-label {
            font-weight: bold;
            color: #92400e;
        }
        .info-val {
            color: #1e293b;
        }

        /* Register Table */
        .register-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #0f172a;
        }
        .register-table th, .register-table td {
            border: 0.5px solid #cbd5e1;
            padding: 1.5px 0.5px;
            text-align: center;
            font-size: 6.5px;
            vertical-align: middle;
        }

        /* Header Rows */
        .th-main-header {
            background-color: #fef3c7;
            color: #78350f;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #0f172a;
        }
        .th-student-col {
            width: 145px;
            max-width: 155px;
            text-align: left !important;
            padding: 2px 6px !important;
            font-size: 7px;
        }
        .th-week {
            background-color: #f8fafc;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #94a3b8;
            padding: 1px 0.5px;
        }
        .th-day {
            width: 18px;
            max-width: 24px;
            background-color: #ffffff;
            font-size: 5.5px;
            font-weight: bold;
            color: #334155;
            padding: 1px 0;
        }
        .th-date {
            display: block;
            font-size: 4.8px;
            color: #94a3b8;
            font-weight: normal;
        }
        .th-summary {
            width: 32px;
            max-width: 38px;
            font-size: 6.5px;
            font-weight: bold;
            background-color: #fef3c7;
            color: #78350f;
            border-left: 1px solid #0f172a;
        }

        /* Beneficiary Row */
        .td-student-details {
            width: 145px;
            max-width: 155px;
            text-align: left !important;
            padding: 2px 4px 2px 6px !important;
            border-right: 1px solid #94a3b8;
            overflow: hidden;
        }
        .student-name {
            text-align: left !important;
            font-weight: bold;
            color: #0f172a;
            font-size: 7px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
        }
        .student-sub {
            text-align: left !important;
            font-size: 5.5px;
            color: #475569;
            line-height: 1.2;
            margin-top: 0.5px;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Cell statuses */
        .cell-served {
            width: 18px;
            max-width: 24px;
            color: #059669;
            font-weight: bold;
            font-size: 7px;
            background-color: #f0fdf4;
            padding: 1px 0;
        }
        .cell-unserved {
            width: 18px;
            max-width: 24px;
            color: #cbd5e1;
            font-size: 6.5px;
            background-color: #ffffff;
            padding: 1px 0;
        }
        .td-total {
            font-weight: bold;
            font-size: 6.5px;
            background-color: #f8fafc;
            border-left: 1px solid #0f172a;
        }

        /* Footer & Signatures */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .footer-table td {
            vertical-align: top;
            padding: 0;
        }
        .legend-box {
            font-size: 6px;
            color: #475569;
            line-height: 1.35;
        }
        .legend-title {
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 1px;
            font-size: 6.5px;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        .sig-box {
            border: 0.5px solid #94a3b8;
            background-color: #ffffff;
            padding: 3px 5px;
            font-size: 6px;
        }
        .sig-line {
            border-bottom: 0.5px dashed #64748b;
            margin-top: 10px;
            margin-bottom: 2px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@php
    $chunks = $beneficiaries->chunk(25);
    $totalPages = $chunks->count() ?: 1;
    $currentPage = 1;

    $dailyTotals = [];
    foreach ($weeksStructure as $weekNum => $days) {
        foreach ($days as $dayMeta) {
            $d = $dayMeta['day_number'];
            $dailyTotals[$d] = 0;
            foreach ($beneficiaries as $b) {
                $dailyTotals[$d] += ($matrix[$b->id][$d] ?? 0);
            }
        }
    }
@endphp

@foreach($chunks as $chunkIndex => $beneficiaryGroup)

    <!-- Top Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="logo-text">PLAY IT FORWARD INSPIRING COMMUNITIES</div>
                <div class="header-subtitle">Official Meal Distribution Register (Cooks) | Kitchen Terminal Logs</div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">FEEDING PROGRAMME ATTENDANCE &amp; DISTRIBUTION RECORD</div>
                <div class="doc-meta">
                    Doc Ref: <strong>{{ $docRef }}</strong> | Generated: {{ $generatedAt }} | Page {{ $currentPage }} of {{ $totalPages }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Metadata Info Bar -->
    <table class="info-box">
        <tr>
            <td>
                <span class="info-label">PROJECT STREAM:</span>
                <span class="info-val"><strong>{{ $project ? $project->name : 'All Projects' }}</strong></span>
            </td>
            <td>
                <span class="info-label">TEAM / CLASS:</span>
                <span class="info-val"><strong>{{ $team ? $team->name : 'All Registered Beneficiaries' }}</strong></span>
            </td>
            <td>
                <span class="info-label">CALENDAR MONTH:</span>
                <span class="info-val"><strong>{{ $monthName }}</strong></span>
            </td>
            <td>
                <span class="info-label">KITCHEN OPERATOR:</span>
                <span class="info-val"><strong>{{ $cooksLabel }}</strong></span>
            </td>
            <td>
                <span class="info-label">TOTAL MEALS SERVED:</span>
                <span class="info-val" style="color: #047857;"><strong>{{ number_format($totalMeals) }}</strong></span>
            </td>
        </tr>
    </table>

    <!-- Register Grid Table -->
    <table class="register-table">
        <thead>
            <!-- Top Grouping Row -->
            <tr>
                <th rowspan="2" style="width: 18px;" class="th-main-header">#</th>
                <th rowspan="2" class="th-main-header th-student-col">BENEFICIARY DETAILS</th>
                @foreach($weeksStructure as $weekNum => $days)
                    <th colspan="{{ count($days) }}" class="th-week">WEEK {{ $weekNum }}</th>
                @endforeach
                <th rowspan="2" class="th-summary">TOTAL MEALS</th>
            </tr>

            <!-- Day & Date Header Row -->
            <tr>
                @foreach($weeksStructure as $weekNum => $days)
                    @foreach($days as $dayMeta)
                        <th class="th-day">
                            {{ $dayMeta['day_label'] }}
                            <span class="th-date">{{ sprintf('%02d', $dayMeta['day_number']) }}</span>
                        </th>
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($beneficiaryGroup as $index => $beneficiary)
                @php
                    $rowNum = (($currentPage - 1) * 25) + $index + 1;
                    $rowTotal = 0;
                @endphp
                <tr>
                    <td style="font-weight: bold; background-color: #f8fafc; font-size: 6px;">{{ $rowNum }}</td>
                    <td class="td-student-details">
                        <div class="student-name">{{ $beneficiary->name }}</div>
                        @php
                            $subParts = [];
                            if ($beneficiary->team) {
                                $subParts[] = $beneficiary->team->name;
                            }
                            if ($beneficiary->relationLoaded('projects') && $beneficiary->projects->isNotEmpty()) {
                                foreach ($beneficiary->projects as $p) {
                                    if ($beneficiary->team && $p->programme_type === \App\Models\Project::PROGRAMME_FOOTBALL) {
                                        continue;
                                    }
                                    if (!in_array($p->name, $subParts)) {
                                        $subParts[] = $p->name;
                                    }
                                }
                            } elseif (!$beneficiary->team) {
                                $fallback = $team ? $team->name : ($project ? $project->name : 'Beneficiary');
                                if ($fallback && !in_array($fallback, $subParts)) {
                                    $subParts[] = $fallback;
                                }
                            }
                            $subParts[] = $beneficiary->shortcode ?: ('PIF-' . str_pad($beneficiary->id, 5, '0', STR_PAD_LEFT));
                            if (!empty($beneficiary->phone_number)) {
                                $subParts[] = $beneficiary->phone_number;
                            }
                            $subLine = implode(' | ', $subParts);
                        @endphp
                        <div class="student-sub">
                            {{ $subLine }}
                        </div>
                    </td>

                    @foreach($weeksStructure as $weekNum => $days)
                        @foreach($days as $dayMeta)
                            @php
                                $d = $dayMeta['day_number'];
                                $count = $matrix[$beneficiary->id][$d] ?? 0;
                                if ($count > 0) {
                                    $rowTotal += $count;
                                }
                            @endphp
                            @if($count > 0)
                                <td class="cell-served">{{ $count > 1 ? $count : '✓' }}</td>
                            @else
                                <td class="cell-unserved">—</td>
                            @endif
                        @endforeach
                    @endforeach

                    <td class="td-total">{{ $rowTotal }}</td>
                </tr>
            @endforeach

            <!-- Fill blank rows if chunk is less than 25 to maintain consistent height -->
            @for($i = $beneficiaryGroup->count(); $i < 25; $i++)
                <tr style="height: 14px;">
                    <td style="color: #cbd5e1; font-size: 5.5px;">{{ (($currentPage - 1) * 25) + $i + 1 }}</td>
                    <td class="td-student-details" style="color: #cbd5e1;">&nbsp;</td>
                    @foreach($weeksStructure as $weekNum => $days)
                        @foreach($days as $dayMeta)
                            <td style="background-color: #ffffff;">&nbsp;</td>
                        @endforeach
                    @endforeach
                    <td style="background-color: #f8fafc;">&nbsp;</td>
                </tr>
            @endfor

            <!-- Daily Totals Summary Row -->
            <tr style="background-color: #fef3c7; font-weight: bold; border-top: 1px solid #0f172a;">
                <td colspan="2" style="text-align: right; padding-right: 6px; font-weight: bold; font-size: 6.5px; color: #78350f;">
                    DAILY TERMINAL MEALS TOTAL:
                </td>
                @foreach($weeksStructure as $weekNum => $days)
                    @foreach($days as $dayMeta)
                        @php $dayTot = $dailyTotals[$dayMeta['day_number']] ?? 0; @endphp
                        <td style="font-weight: bold; font-size: 6px; color: {{ $dayTot > 0 ? '#047857' : '#94a3b8' }};">
                            {{ $dayTot > 0 ? $dayTot : '—' }}
                        </td>
                    @endforeach
                @endforeach
                <td style="background-color: #fde68a; font-weight: bold; font-size: 7px; color: #047857;">
                    {{ $totalMeals }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer: Legend & Verification Signatures -->
    <table class="footer-table">
        <tr>
            <!-- Left: Legend & Compliance Note -->
            <td style="width: 32%; padding-right: 6px;">
                <div class="legend-box">
                    <div class="legend-title">Cook Terminal Legend | Feeding Metric</div>
                    <div><strong>&check;</strong> = Meal distributed and confirmed at kitchen terminal</div>
                    <div><strong>—</strong> = No meal distribution recorded for beneficiary on this date</div>
                    <div style="margin-top: 2px; color: #64748b; font-size: 5.5px;">
                        * Sourced directly from <em>Cook Terminal</em> transactions. Terminal restricted to cooks.
                    </div>
                </div>
            </td>

            <!-- Right: 3 Signatures -->
            <td style="width: 68%;">
                <table class="sig-table">
                    <tr>
                        <td style="width: 33%; padding-right: 4px;">
                            <div class="sig-box">
                                <strong>COOK / KITCHEN OPERATOR:</strong>
                                <div class="sig-line"></div>
                                <span>Sign &amp; Date (Cook In-Charge)</span>
                            </div>
                        </td>
                        <td style="width: 33%; padding-right: 4px;">
                            <div class="sig-box">
                                <strong>PROJECT OFFICER / COACH:</strong>
                                <div class="sig-line"></div>
                                <span>Sign &amp; Date (Centre Supervisor)</span>
                            </div>
                        </td>
                        <td style="width: 34%;">
                            <div class="sig-box">
                                <strong>MEAL OFFICER:</strong>
                                <div class="sig-line"></div>
                                <span>Sign &amp; Date (Programme Audit)</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

    @php $currentPage++; @endphp

@endforeach

</body>
</html>
