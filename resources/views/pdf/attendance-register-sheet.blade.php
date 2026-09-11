<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Attendance Register - {{ $project ? $project->name : 'Play It Forward' }}</title>
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
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 1px;
            border-bottom: 2px solid #059669;
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
            background-color: #f8fafc;
            margin-bottom: 4px;
        }
        .info-box td {
            padding: 3px 6px;
            font-size: 7.5px;
            border-right: 1px solid #e2e8f0;
        }
        .info-box td:last-child {
            border-right: none;
        }
        .info-label {
            font-weight: bold;
            color: #0f172a;
        }
        .info-val {
            color: #334155;
        }

        /* Attendance Table */
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
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #0f172a;
        }
        .th-student-col {
            width: 220px;
            min-width: 200px;
            text-align: left;
            padding: 2px 6px;
            font-size: 7.5px;
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
            width: 24px;
            max-width: 30px;
            font-size: 6.5px;
            font-weight: bold;
            background-color: #f8fafc;
            border-left: 1px solid #0f172a;
        }

        /* Student Row */
        .td-student-details {
            width: 220px;
            min-width: 200px;
            text-align: left;
            padding: 2px 4px 2px 6px;
            border-right: 1px solid #94a3b8;
            overflow: hidden;
        }
        .student-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 7.5px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
        }
        .student-sub {
            font-size: 6px;
            color: #475569;
            line-height: 1.2;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Cell statuses */
        .cell-present {
            width: 18px;
            max-width: 24px;
            color: #059669;
            font-weight: bold;
            font-size: 7px;
            background-color: #f0fdf4;
            padding: 1px 0;
        }
        .cell-absent {
            width: 18px;
            max-width: 24px;
            color: #e11d48;
            font-weight: bold;
            font-size: 6.5px;
            background-color: #fff1f2;
            padding: 1px 0;
        }
        .cell-late {
            width: 18px;
            max-width: 24px;
            color: #d97706;
            font-weight: bold;
            font-size: 6px;
            background-color: #fffbeb;
            padding: 1px 0;
        }
        .cell-apology {
            width: 18px;
            max-width: 24px;
            color: #2563eb;
            font-weight: bold;
            font-size: 6px;
            background-color: #eff6ff;
            padding: 1px 0;
        }
        .cell-unmarked {
            width: 18px;
            max-width: 24px;
            color: #cbd5e1;
            font-size: 6px;
            padding: 1px 0;
        }

        /* Summary counters */
        .td-p {
            width: 24px;
            max-width: 30px;
            color: #047857;
            font-weight: bold;
            font-size: 6.8px;
            background-color: #f0fdf4;
            border-left: 1px solid #0f172a;
        }
        .td-a {
            width: 24px;
            max-width: 30px;
            color: #be123c;
            font-weight: bold;
            font-size: 6.8px;
            background-color: #fff1f2;
        }
        .td-rate {
            width: 28px;
            max-width: 35px;
            color: #0f172a;
            font-weight: bold;
            font-size: 6.8px;
            background-color: #f8fafc;
        }

        /* Aggregate rows */
        .tr-aggregate {
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 1px solid #0f172a;
        }
        .td-aggregate-label {
            width: 220px;
            min-width: 200px;
            text-align: left;
            padding-left: 6px;
            font-size: 6.5px;
            text-transform: uppercase;
            font-weight: bold;
            border-right: 1px solid #94a3b8;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Footer Section */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .footer-table td {
            vertical-align: top;
            padding: 0;
        }
        .footer-heading {
            font-size: 7px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .ruled-line {
            border-bottom: 0.5px dotted #94a3b8;
            height: 10px;
            width: 95%;
            margin-bottom: 1.5px;
        }
        .sig-line {
            border-bottom: 0.5px solid #64748b;
            height: 14px;
            width: 90%;
            margin-bottom: 1.5px;
        }
        .sig-sub {
            font-size: 6px;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Legend Bar */
        .legend-bar {
            margin-top: 5px;
            border-top: 0.5px solid #cbd5e1;
            padding-top: 2px;
            font-size: 6px;
            color: #475569;
        }
        .legend-item {
            margin-right: 10px;
        }
    </style>
</head>
<body>

    {{-- Top Header Section (Image 3 style) --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="logo-text">play it forward</div>
                <div class="header-subtitle">
                    {{ $project ? strtoupper($project->name) : 'PLAY IT FORWARD PROGRAMMES' }} &bull; OFFICIAL ATTENDANCE REGISTER
                </div>
            </td>
            <td style="width: 40%;">
                <div class="doc-title">SCHEDULE ATTENDANCE SHEET (UP TO CURRENT DATE)</div>
                <div class="doc-meta">Generated: {{ $generatedAt }} &bull; Doc: {{ $docRef }}</div>
            </td>
        </tr>
    </table>

    {{-- Metadata Info Box (Image 3 style) --}}
    <table class="info-box">
        <tr>
            @php
                $isFootball = ($project && $project->programme_type === \App\Models\Project::PROGRAMME_FOOTBALL)
                    || ($team && $team->project && $team->project->programme_type === \App\Models\Project::PROGRAMME_FOOTBALL);
                $groupLabel = $isFootball ? 'Team' : 'Class';
                $groupVal = $team ? $team->name : ($project ? $project->name : 'General Roster');
                $personLabel = $isFootball ? 'Coach' : 'Instructor';
            @endphp
            <td style="width: 28%;">
                <span class="info-label">Programme:</span> 
                <span class="info-val">{{ $project ? $project->name : 'All Programmes' }}</span>
            </td>
            <td style="width: 26%;">
                <span class="info-label">{{ $groupLabel }}:</span> 
                <span class="info-val">{{ $groupVal }}</span>
            </td>
            <td style="width: 28%;">
                <span class="info-label">{{ $personLabel }}:</span> 
                <span class="info-val">{{ $instructorName }}</span>
            </td>
            <td style="width: 18%; text-align: right;">
                <span class="info-label">Enrolled Students:</span> 
                <span class="info-val" style="font-weight: bold;">{{ $beneficiaries->count() }}</span>
            </td>
        </tr>
    </table>

    {{-- Flatten all active calendar days across weeks --}}
    @php
        $allActiveDays = [];
        $totalDaysCount = 0;
        foreach ($weeksStructure as $wNum => $daysList) {
            foreach ($daysList as $dMeta) {
                $allActiveDays[] = $dMeta['day_number'];
                $totalDaysCount++;
            }
        }

        // Daily aggregates tracker
        $dailyPresentCounts = array_fill_keys($allActiveDays, 0);
        $dailyAbsentCounts  = array_fill_keys($allActiveDays, 0);
        $totalSessionsHeld  = 0;
    @endphp

    {{-- Main Attendance Grid (Image 3 layout) --}}
    <table class="register-table">
        <thead>
            {{-- Top Header Row: Student Details | MONTH: MONTH YEAR | Summary cols --}}
            <tr>
                <th class="th-main-header th-student-col" rowspan="3">
                    STUDENT DETAILS<br>
                    <span style="font-size: 5.5px; font-weight: normal; color: #64748b;">NAME &bull; {{ $isFootball ? 'TEAM' : 'CLASS' }} &bull; BENEFICIARY CODE</span>
                </th>
                <th class="th-main-header" colspan="{{ $totalDaysCount }}">
                    MONTH: {{ strtoupper($monthName) }}
                </th>
                <th class="th-summary" rowspan="3" title="Total Present Sessions">P</th>
                <th class="th-summary" rowspan="3" title="Total Absent Sessions">A</th>
                <th class="th-summary" rowspan="3" title="Attendance Percentage">%</th>
            </tr>

            {{-- Week Headers Row --}}
            <tr>
                @foreach($weeksStructure as $weekNum => $daysList)
                    <th class="th-week" colspan="{{ count($daysList) }}">
                        Week {{ $weekNum }}
                    </th>
                @endforeach
            </tr>

            {{-- Day & Date Headers Row (Col 1 & Summary cols covered by rowspan 3) --}}
            <tr>
                @foreach($weeksStructure as $weekNum => $daysList)
                    @foreach($daysList as $dMeta)
                        <th class="th-day">
                            {{ $dMeta['day_label'] }}
                            <span class="th-date">{{ sprintf('%02d', $dMeta['day_number']) }}</span>
                        </th>
                    @endforeach
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($beneficiaries as $index => $beneficiary)
                @php
                    $studentPresent = 0;
                    $studentAbsent  = 0;
                    $studentLate    = 0;
                    $studentApology = 0;
                    $studentTotalMarked = 0;
                @endphp
                <tr>
                    {{-- Student Identification --}}
                    <td class="td-student-details">
                        <div class="student-name">{{ $index + 1 }}. {{ $beneficiary->name }}</div>
                        <div class="student-sub">
                            @php
                                $bTeam = $beneficiary->team ? $beneficiary->team->name : ($team ? $team->name : ($project ? $project->name : 'PIF'));
                                $bCode = $beneficiary->shortcode ?: ('PIF-' . str_pad($beneficiary->id, 5, '0', STR_PAD_LEFT));
                            @endphp
                            {{ $isFootball ? 'Team' : 'Class' }}: {{ $bTeam }} &bull; Code: {{ $bCode }}
                        </div>
                    </td>

                    {{-- Daily Status Cells --}}
                    @foreach($allActiveDays as $dayNum)
                        @php
                            $status = $matrix[$beneficiary->id][$dayNum] ?? null;
                            if ($status === 'present') {
                                $studentPresent++;
                                $studentTotalMarked++;
                                $dailyPresentCounts[$dayNum]++;
                            } elseif ($status === 'late') {
                                $studentLate++;
                                $studentPresent++;
                                $studentTotalMarked++;
                                $dailyPresentCounts[$dayNum]++;
                            } elseif ($status === 'absent') {
                                $studentAbsent++;
                                $studentTotalMarked++;
                                $dailyAbsentCounts[$dayNum]++;
                            } elseif ($status === 'apology') {
                                $studentApology++;
                                $studentTotalMarked++;
                                $dailyAbsentCounts[$dayNum]++;
                            }
                        @endphp

                        @if($status === 'present')
                            <td class="cell-present">&#10003;</td>
                        @elseif($status === 'absent')
                            <td class="cell-absent">&#10007;</td>
                        @elseif($status === 'late')
                            <td class="cell-late">L</td>
                        @elseif($status === 'apology')
                            <td class="cell-apology">E</td>
                        @else
                            <td class="cell-unmarked">&middot;</td>
                        @endif
                    @endforeach

                    {{-- Summary Columns (P, A, %) --}}
                    @php
                        $rate = $studentTotalMarked > 0 ? round(($studentPresent / $studentTotalMarked) * 100) : 0;
                    @endphp
                    <td class="td-p">{{ $studentPresent }}</td>
                    <td class="td-a">{{ $studentAbsent }}</td>
                    <td class="td-rate">{{ $rate }}%</td>
                </tr>
            @endforeach

            {{-- Daily Aggregates: DAILY PRESENT (✓) --}}
            @php
                $activeDatesWithAttendance = 0;
                foreach ($allActiveDays as $dayNum) {
                    if ($dailyPresentCounts[$dayNum] > 0 || $dailyAbsentCounts[$dayNum] > 0) {
                        $activeDatesWithAttendance++;
                    }
                }
            @endphp
            <tr class="tr-aggregate">
                <td class="td-aggregate-label">DAILY PRESENT (&#10003;)</td>
                @foreach($allActiveDays as $dayNum)
                    <td style="font-weight: bold; color: #047857;">
                        {{ $dailyPresentCounts[$dayNum] > 0 ? $dailyPresentCounts[$dayNum] : '·' }}
                    </td>
                @endforeach
                <td colspan="3" style="font-size: 6px; font-weight: bold; text-align: center; background-color: #f1f5f9;">
                    {{ $activeDatesWithAttendance }} Sessions
                </td>
            </tr>

            {{-- Daily Aggregates: DAILY ABSENT (✗) --}}
            <tr class="tr-aggregate">
                <td class="td-aggregate-label">DAILY ABSENT (&#10007;)</td>
                @foreach($allActiveDays as $dayNum)
                    <td style="font-weight: bold; color: #be123c;">
                        {{ $dailyAbsentCounts[$dayNum] > 0 ? $dailyAbsentCounts[$dayNum] : '0' }}
                    </td>
                @endforeach
                <td colspan="3" style="font-size: 6px; font-weight: bold; text-align: center; background-color: #f1f5f9;">
                    Aggregated
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Footer Section (Image 3 style) --}}
    <table class="footer-table">
        <tr>
            {{-- Left Box: Coach / Instructor Notes & Remarks --}}
            <td style="width: 55%;">
                <div class="footer-heading">{{ $personLabel }} NOTES &amp; ATTENDANCE REMARKS:</div>
                <div class="ruled-line"></div>
                <div class="ruled-line"></div>
                <div class="ruled-line"></div>
            </td>

            {{-- Spacer --}}
            <td style="width: 5%;"></td>

            {{-- Right Box: Signatures & Official Attestation --}}
            <td style="width: 40%;">
                <div class="footer-heading">SIGNATURES &amp; OFFICIAL ATTESTATION:</div>
                <div class="sig-line"></div>
                <div class="sig-sub">{{ $isFootball ? 'COACH SIGNATURE • DATE' : 'CLASS INSTRUCTOR SIGNATURE • DATE' }}</div>
                <div style="height: 5px;"></div>
                <div class="sig-line"></div>
                <div class="sig-sub">HEAD OF PROGRAMMES / QUALITY VERIFIER &bull; DATE</div>
            </td>
        </tr>
    </table>

    {{-- Register Legend Bar (Image 3 bottom style) --}}
    <div class="legend-bar">
        <strong>Register Legend:</strong>
        <span class="legend-item" style="color: #059669; font-weight: bold;">&#10003; = Present</span>
        <span class="legend-item" style="color: #e11d48; font-weight: bold;">&#10007; = Absent</span>
        <span class="legend-item" style="color: #d97706; font-weight: bold;">L = Late Arrival</span>
        <span class="legend-item" style="color: #2563eb; font-weight: bold;">E = Apology / Excused</span>
        <span class="legend-item" style="color: #94a3b8;">&middot; = Unmarked</span>
        <span class="legend-item" style="color: #94a3b8;">— = No Session Scheduled</span>
    </div>

</body>
</html>
