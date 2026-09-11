<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Team;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

echo "Initial memory_limit: " . ini_get('memory_limit') . "\n";

$beneficiaries = Beneficiary::all();
echo "Beneficiaries count: " . $beneficiaries->count() . "\n";

// Set memory limit to 512M
ini_set('memory_limit', '512M');
set_time_limit(300);
echo "New memory_limit: " . ini_get('memory_limit') . "\n";

// Weeks structure
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

$startMem = memory_get_usage(true);
$pdf = Pdf::loadView('pdf.attendance-register-sheet', [
    'project'        => Project::first(),
    'team'           => Team::first(),
    'beneficiaries'  => $beneficiaries,
    'weeksStructure' => $weeks,
    'matrix'         => [],
    'month'          => 9,
    'year'           => 2026,
    'monthName'      => 'September 2026',
    'instructorName' => 'Coach Peter Banda',
    'generatedAt'    => now()->format('d M Y, H:i'),
    'docRef'         => 'PIF-TEST-ALL',
])->setPaper('a4', 'landscape');

$out = $pdf->output();
$endMem = memory_get_peak_usage(true);
echo "PDF generated! Size: " . strlen($out) . " bytes\n";
echo "Peak memory used: " . round($endMem / 1024 / 1024, 2) . " MB\n";
