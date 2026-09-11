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

// Let's test with month = 9, year = 2026 (current month in user's system)
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

$pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.attendance-register-sheet', [
    'project' => $project,
    'team' => $team,
    'beneficiaries' => $beneficiaries,
    'weeksStructure' => $weeksStructure,
    'matrix' => [],
    'month' => $month,
    'year' => $year,
    'monthName' => 'September 2026',
    'instructorName' => 'Coach Webster Mweemba',
    'generatedAt' => date('d M Y, H:i'),
    'docRef' => 'PIF-REG-U17GIRLS',
])->setPaper('a4', 'landscape');

$html = view('pdf.attendance-register-sheet', [
    'project' => $project,
    'team' => $team,
    'beneficiaries' => $beneficiaries,
    'weeksStructure' => $weeksStructure,
    'matrix' => [],
    'month' => $month,
    'year' => $year,
    'monthName' => 'September 2026',
    'instructorName' => 'Coach Webster Mweemba',
    'generatedAt' => date('d M Y, H:i'),
    'docRef' => 'PIF-REG-U17GIRLS',
])->render();

file_put_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/u17_rendered.html', $html);
file_put_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/u17_test.pdf', $pdf->output());

echo "Done! Total days: " . count($weeksStructure) . " weeks\n";
