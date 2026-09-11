<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';
$app = require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Beneficiary;

$project = Project::where('programme_type', Project::PROGRAMME_EDUCATION)->first();
$beneficiaries = Beneficiary::limit(12)->get();

$weeksStructure = [
    1 => [
        ['day_number' => 1, 'day_label' => 'M', 'full_date' => '2026-09-01'],
        ['day_number' => 2, 'day_label' => 'T', 'full_date' => '2026-09-02'],
        ['day_number' => 3, 'day_label' => 'W', 'full_date' => '2026-09-03'],
        ['day_number' => 4, 'day_label' => 'TH', 'full_date' => '2026-09-04'],
        ['day_number' => 5, 'day_label' => 'F', 'full_date' => '2026-09-05'],
    ],
    2 => [
        ['day_number' => 8, 'day_label' => 'M', 'full_date' => '2026-09-08'],
        ['day_number' => 9, 'day_label' => 'T', 'full_date' => '2026-09-09'],
        ['day_number' => 10, 'day_label' => 'W', 'full_date' => '2026-09-10'],
        ['day_number' => 11, 'day_label' => 'TH', 'full_date' => '2026-09-11'],
        ['day_number' => 12, 'day_label' => 'F', 'full_date' => '2026-09-12'],
    ],
    3 => [
        ['day_number' => 15, 'day_label' => 'M', 'full_date' => '2026-09-15'],
        ['day_number' => 16, 'day_label' => 'T', 'full_date' => '2026-09-16'],
        ['day_number' => 17, 'day_label' => 'W', 'full_date' => '2026-09-17'],
        ['day_number' => 18, 'day_label' => 'TH', 'full_date' => '2026-09-18'],
        ['day_number' => 19, 'day_label' => 'F', 'full_date' => '2026-09-19'],
    ],
    4 => [
        ['day_number' => 22, 'day_label' => 'M', 'full_date' => '2026-09-22'],
        ['day_number' => 23, 'day_label' => 'T', 'full_date' => '2026-09-23'],
        ['day_number' => 24, 'day_label' => 'W', 'full_date' => '2026-09-24'],
        ['day_number' => 25, 'day_label' => 'TH', 'full_date' => '2026-09-25'],
        ['day_number' => 26, 'day_label' => 'F', 'full_date' => '2026-09-26'],
    ],
    5 => [
        ['day_number' => 29, 'day_label' => 'M', 'full_date' => '2026-09-29'],
        ['day_number' => 30, 'day_label' => 'T', 'full_date' => '2026-09-30'],
    ],
];

$pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.attendance-register-sheet', [
    'project' => $project,
    'team' => null,
    'beneficiaries' => $beneficiaries,
    'weeksStructure' => $weeksStructure,
    'matrix' => [],
    'month' => 9,
    'year' => 2026,
    'monthName' => 'September 2026',
    'instructorName' => 'Ruth Musonda (Project Officer)',
    'generatedAt' => date('d M Y, H:i'),
    'docRef' => 'PIF-REG-LIT-202609',
])->setPaper('a4', 'landscape');

$output = $pdf->output();
file_put_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/test_lit_out.pdf', $output);
echo "Generated Literacy PDF successfully. Size: " . strlen($output) . " bytes\n";
