<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';
$app = require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Team;
use App\Models\Beneficiary;

$month = 8;
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

$project = Project::first();
$team = Team::first();
$beneficiaries = Beneficiary::limit(15)->get();

$matrix = [];
foreach ($beneficiaries as $b) {
    for ($d = 1; $d <= 31; $d++) {
        if (rand(0, 10) > 2) {
            $matrix[$b->id][$d] = 'present';
        } elseif (rand(0, 10) > 5) {
            $matrix[$b->id][$d] = 'absent';
        }
    }
}

$pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.attendance-register-sheet', [
    'project' => $project,
    'team' => $team,
    'beneficiaries' => $beneficiaries,
    'weeksStructure' => $weeksStructure,
    'matrix' => $matrix,
    'month' => $month,
    'year' => $year,
    'monthName' => 'August 2026',
    'instructorName' => 'Coach Webster Mweemba',
    'generatedAt' => date('d M Y, H:i'),
    'docRef' => 'PIF-REG-TEST-202608',
])->setPaper('a4', 'landscape');

$output = $pdf->output();
file_put_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/test_out.pdf', $output);
echo "Generated PDF successfully. Weeks count: " . count($weeksStructure) . "\n";
foreach ($weeksStructure as $w => $days) {
    echo "Week $w: " . count($days) . " days\n";
}
