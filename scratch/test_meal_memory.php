<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Team;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

ini_set('memory_limit', '512M');
set_time_limit(300);

$beneficiaries = Beneficiary::all();
echo "Beneficiaries count: " . $beneficiaries->count() . "\n";

$start = Carbon::createFromDate(2026, 9, 1)->startOfMonth();
$end = $start->copy()->endOfMonth();
$curr = $start->copy();
$weeks = [];
$weekNum = 1;
$daysCol = [];
while ($curr->lte($end)) {
    if ($curr->isWeekday()) {
        $daysCol[] = [
            'day_number' => $curr->day,
            'day_label'  => 'M',
            'full_date' => $curr->toDateString(),
        ];
    }
    if ($curr->dayOfWeek === Carbon::FRIDAY || $curr->copy()->addDay()->month !== 9) {
        if (!empty($daysCol)) {
            $weeks[$weekNum] = $daysCol;
            $weekNum++;
            $daysCol = [];
        }
    }
    $curr->addDay();
}

$pdfMeal = Pdf::loadView('pdf.meal-distribution-register-sheet', [
    'project'        => Project::first(),
    'team'           => Team::first(),
    'beneficiaries'  => $beneficiaries,
    'weeksStructure' => $weeks,
    'matrix'         => [],
    'month'          => 9,
    'year'           => 2026,
    'monthName'      => 'September 2026',
    'cooksLabel'     => 'Kitchen Cook Terminal',
    'totalMeals'     => 150,
    'generatedAt'    => now()->format('d M Y, H:i'),
    'docRef'         => 'PIF-MEAL-TEST-ALL',
])->setPaper('a4', 'landscape');

$outMeal = $pdfMeal->output();
$endMem = memory_get_peak_usage(true);
echo "Meal PDF generated! Size: " . strlen($outMeal) . " bytes\n";
echo "Peak memory used: " . round($endMem / 1024 / 1024, 2) . " MB\n";
