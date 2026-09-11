<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';
$app = require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = App\Models\Project::first();
$team = App\Models\Team::first();
$beneficiaries = App\Models\Beneficiary::limit(10)->get();

$weeksStructure = [
    1 => [
        ['day_number' => 3, 'day_label' => 'M', 'full_date' => '2026-08-03'],
        ['day_number' => 4, 'day_label' => 'T', 'full_date' => '2026-08-04'],
        ['day_number' => 5, 'day_label' => 'W', 'full_date' => '2026-08-05'],
        ['day_number' => 6, 'day_label' => 'TH', 'full_date' => '2026-08-06'],
        ['day_number' => 7, 'day_label' => 'F', 'full_date' => '2026-08-07'],
    ],
    2 => [
        ['day_number' => 10, 'day_label' => 'M', 'full_date' => '2026-08-10'],
        ['day_number' => 11, 'day_label' => 'T', 'full_date' => '2026-08-11'],
        ['day_number' => 12, 'day_label' => 'W', 'full_date' => '2026-08-12'],
        ['day_number' => 13, 'day_label' => 'TH', 'full_date' => '2026-08-13'],
        ['day_number' => 14, 'day_label' => 'F', 'full_date' => '2026-08-14'],
    ],
    3 => [
        ['day_number' => 17, 'day_label' => 'M', 'full_date' => '2026-08-17'],
        ['day_number' => 18, 'day_label' => 'T', 'full_date' => '2026-08-18'],
        ['day_number' => 19, 'day_label' => 'W', 'full_date' => '2026-08-19'],
        ['day_number' => 20, 'day_label' => 'TH', 'full_date' => '2026-08-20'],
        ['day_number' => 21, 'day_label' => 'F', 'full_date' => '2026-08-21'],
    ],
    4 => [
        ['day_number' => 24, 'day_label' => 'M', 'full_date' => '2026-08-24'],
        ['day_number' => 25, 'day_label' => 'T', 'full_date' => '2026-08-25'],
        ['day_number' => 26, 'day_label' => 'W', 'full_date' => '2026-08-26'],
        ['day_number' => 27, 'day_label' => 'TH', 'full_date' => '2026-08-27'],
        ['day_number' => 28, 'day_label' => 'F', 'full_date' => '2026-08-28'],
    ],
    5 => [
        ['day_number' => 31, 'day_label' => 'M', 'full_date' => '2026-08-31'],
    ],
];

$html = view('pdf.attendance-register-sheet', [
    'project' => $project,
    'team' => $team,
    'beneficiaries' => $beneficiaries,
    'weeksStructure' => $weeksStructure,
    'matrix' => [],
    'month' => 8,
    'year' => 2026,
    'monthName' => 'August 2026',
    'instructorName' => 'Coach Webster Mweemba',
    'generatedAt' => date('d M Y, H:i'),
    'docRef' => 'PIF-REG-TEST-202608',
])->render();

file_put_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/rendered_test.html', $html);
echo "HTML rendered: " . strlen($html) . " bytes\n";
