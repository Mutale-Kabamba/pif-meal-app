<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';
$app = require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Team;
use App\Models\Beneficiary;

$team = Team::find(7); // U17 Girls
$project = $team->project;
$beneficiaries = Beneficiary::where('team_id', 7)->get();

$month = 9;
$year = 2026;
$startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
$endOfMonth   = $startOfMonth->copy()->endOfMonth();

$weeksStructure = [];
$currentDate = $startOfMonth->copy();
$currentWeekNumber = 1;
$weekDaysCollection = [];

while ($currentDate->lte($endOfMonth)) {
    if ($currentDate->isWeekday()) {
        $weekDaysCollection[] = [
            'day_number' => $currentDate->day,
            'day_label'  => match ($currentDate->dayOfWeek) {
                Carbon::MONDAY    => 'M',
                Carbon::TUESDAY   => 'T',
                Carbon::WEDNESDAY => 'W',
                Carbon::THURSDAY  => 'TH',
                Carbon::FRIDAY    => 'F',
            },
            'full_date' => $currentDate->toDateString(),
        ];
    }

    if ($currentDate->dayOfWeek === Carbon::FRIDAY || $currentDate->copy()->addDay()->month !== $month) {
        if (!empty($weekDaysCollection)) {
            $weeksStructure[$currentWeekNumber] = $weekDaysCollection;
            $currentWeekNumber++;
            $weekDaysCollection = [];
        }
    }

    $currentDate->addDay();
}

$matrix = [];
foreach ($beneficiaries as $b) {
    for ($d = 1; $d <= 30; $d++) {
        $r = rand(1, 10);
        if ($r <= 7) $matrix[$b->id][$d] = 'present';
        elseif ($r == 8) $matrix[$b->id][$d] = 'late';
        elseif ($r == 9) $matrix[$b->id][$d] = 'absent';
        else $matrix[$b->id][$d] = 'apology';
    }
}

// Let's test widths: 140px, 150px
$widths = [135, 145, 155];

foreach ($widths as $w) {
    $html = view('pdf.attendance-register-sheet', [
        'project' => $project,
        'team' => $team,
        'beneficiaries' => $beneficiaries,
        'weeksStructure' => $weeksStructure,
        'matrix' => $matrix,
        'month' => $month,
        'year' => $year,
        'monthName' => 'September 2026',
        'instructorName' => 'Coach Webster Mweemba',
        'generatedAt' => date('d M Y, H:i'),
        'docRef' => 'PIF-REG-U17GIRLS',
    ])->render();

    // Modify width to $w and ensure left alignment
    $html = str_replace('width: 220px; min-width: 200px;', "width: {$w}px; max-width: {$w}px;", $html);
    $html = str_replace('.th-student-col {', ".th-student-col { text-align: left !important; padding-left: 6px !important; ", $html);
    $html = str_replace('.td-student-details {', ".td-student-details { text-align: left !important; padding-left: 6px !important; ", $html);
    $html = str_replace('.student-name {', ".student-name { text-align: left !important; ", $html);
    $html = str_replace('.student-sub {', ".student-sub { text-align: left !important; ", $html);
    $html = str_replace('.td-aggregate-label {', ".td-aggregate-label { text-align: left !important; padding-left: 6px !important; ", $html);

    $dompdf = new Dompdf\Dompdf();
    $dompdf->setPaper('a4', 'landscape');
    $dompdf->loadHtml($html);
    $dompdf->render();

    file_put_contents("scratch/test_w{$w}.pdf", $dompdf->output());
}

echo "Generated test PDFs for 135, 145, 155\n";
