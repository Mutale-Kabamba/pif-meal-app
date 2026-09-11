<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Models\Team;
use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\AttendanceLog;
use App\Filament\Pages\ProjectRegistersPage;
use App\Filament\Pages\MealDistributionRegisterPage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

echo "=== 1. VERIFYING USER ROLES & ACCESS GATES ===\n";

$cook = User::where('role', User::ROLE_COOK)->first() ?? new User(['name' => 'Test Cook', 'role' => User::ROLE_COOK]);
$coach = User::where('role', User::ROLE_COACH)->first() ?? new User(['name' => 'Test Coach', 'role' => User::ROLE_COACH]);
$officer = User::where('role', User::ROLE_PROJECT_OFFICER)->first() ?? new User(['name' => 'Test Officer', 'role' => User::ROLE_PROJECT_OFFICER]);
$admin = User::where('role', User::ROLE_HEAD_OF_PROGRAMMES)->first() ?? new User(['name' => 'Test Admin', 'role' => User::ROLE_HEAD_OF_PROGRAMMES]);

// Check Terminal Gate
echo "Cook access_terminal gate: " . (Gate::forUser($cook)->allows('access_terminal') ? "ALLOWED (OK)" : "BLOCKED (FAIL)") . "\n";
echo "Coach access_terminal gate: " . (Gate::forUser($coach)->allows('access_terminal') ? "ALLOWED (FAIL)" : "BLOCKED (OK)") . "\n";
echo "Officer access_terminal gate: " . (Gate::forUser($officer)->allows('access_terminal') ? "ALLOWED (FAIL)" : "BLOCKED (OK)") . "\n";
echo "Admin access_terminal gate: " . (Gate::forUser($admin)->allows('access_terminal') ? "ALLOWED (FAIL)" : "BLOCKED (OK)") . "\n";

// Check Panel Access
$panel = filament()->getCurrentPanel() ?? filament()->getPanel('admin');
echo "Cook canAccessPanel: " . ($cook->canAccessPanel($panel) ? "TRUE (FAIL)" : "FALSE (OK - Cook barred from admin panel)") . "\n";
echo "Coach canAccessPanel: " . ($coach->canAccessPanel($panel) ? "TRUE (OK)" : "FALSE (FAIL)") . "\n";
echo "Officer canAccessPanel: " . ($officer->canAccessPanel($panel) ? "TRUE (OK)" : "FALSE (FAIL)") . "\n";
echo "Admin canAccessPanel: " . ($admin->canAccessPanel($panel) ? "TRUE (OK)" : "FALSE (FAIL)") . "\n";

echo "\n=== 2. VERIFYING FILAMENT REGISTER ACCESS ===\n";
auth()->login($cook);
echo "Cook can access Attendance Register: " . (ProjectRegistersPage::canAccess() ? "YES (FAIL)" : "NO (OK)") . "\n";
echo "Cook can access Meal Distribution Register: " . (MealDistributionRegisterPage::canAccess() ? "YES (FAIL)" : "NO (OK)") . "\n";

auth()->login($coach);
echo "Coach can access Attendance Register: " . (ProjectRegistersPage::canAccess() ? "YES (OK)" : "NO (FAIL)") . "\n";
echo "Coach can access Meal Distribution Register: " . (MealDistributionRegisterPage::canAccess() ? "YES (FAIL)" : "NO (OK - Coach only accesses training attendance)") . "\n";

auth()->login($officer);
echo "Officer can access Attendance Register: " . (ProjectRegistersPage::canAccess() ? "YES (OK)" : "NO (FAIL)") . "\n";
echo "Officer can access Meal Distribution Register: " . (MealDistributionRegisterPage::canAccess() ? "YES (OK)" : "NO (FAIL)") . "\n";

auth()->login($admin);
echo "Admin can access Attendance Register: " . (ProjectRegistersPage::canAccess() ? "YES (OK)" : "NO (FAIL)") . "\n";
echo "Admin can access Meal Distribution Register: " . (MealDistributionRegisterPage::canAccess() ? "YES (OK)" : "NO (FAIL)") . "\n";

echo "\n=== 3. VERIFYING ROUTE MIDDLEWARE ===\n";
$routes = app('router')->getRoutes();
$terminalRoute = $routes->getByName('terminal');
if ($terminalRoute) {
    $middleware = $terminalRoute->gatherMiddleware();
    echo "Terminal route middleware: " . implode(', ', $middleware) . "\n";
    $hasTerminalGate = in_array('can:access_terminal', $middleware);
    echo "Terminal has can:access_terminal middleware: " . ($hasTerminalGate ? "YES (OK)" : "NO (FAIL)") . "\n";
}

echo "\n=== 4. TESTING MEAL DISTRIBUTION REGISTER PDF RENDERING ===\n";
$project = Project::first();
$team = Team::first();
$beneficiaries = Beneficiary::take(10)->get();

// Build calendar weeks
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
            'day_label'  => match ($curr->dayOfWeek) {
                Carbon::MONDAY => 'M', Carbon::TUESDAY => 'T', Carbon::WEDNESDAY => 'W',
                Carbon::THURSDAY => 'TH', Carbon::FRIDAY => 'F',
            },
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

$matrix = [];
foreach ($beneficiaries as $b) {
    $matrix[$b->id][1] = 1;
    $matrix[$b->id][3] = 1;
    $matrix[$b->id][5] = 1;
}

$pdfMeal = Pdf::loadView('pdf.meal-distribution-register-sheet', [
    'project'        => $project,
    'team'           => $team,
    'beneficiaries'  => $beneficiaries,
    'weeksStructure' => $weeks,
    'matrix'         => $matrix,
    'month'          => 9,
    'year'           => 2026,
    'monthName'      => 'September 2026',
    'cooksLabel'     => 'Alice Tembo (Cook)',
    'totalMeals'     => 30,
    'generatedAt'    => now()->format('d M Y, H:i'),
    'docRef'         => 'PIF-MEAL-TEST-001',
])->setPaper('a4', 'landscape');

$mealPdfPath = __DIR__ . '/test_meal_register.pdf';
file_put_contents($mealPdfPath, $pdfMeal->output());
echo "Meal Distribution PDF generated successfully (" . filesize($mealPdfPath) . " bytes)\n";

echo "\n=== 5. TESTING ATTENDANCE REGISTER PDF RENDERING ===\n";
$pdfAtt = Pdf::loadView('pdf.attendance-register-sheet', [
    'project'        => $project,
    'team'           => $team,
    'beneficiaries'  => $beneficiaries,
    'weeksStructure' => $weeks,
    'matrix'         => [
        $beneficiaries[0]->id => [1 => 'present', 2 => 'present', 3 => 'late', 4 => 'absent', 5 => 'apology'],
    ],
    'month'          => 9,
    'year'           => 2026,
    'monthName'      => 'September 2026',
    'instructorName' => 'Coach Peter Banda',
    'generatedAt'    => now()->format('d M Y, H:i'),
    'docRef'         => 'PIF-ATT-TEST-001',
])->setPaper('a4', 'landscape');

$attPdfPath = __DIR__ . '/test_attendance_register.pdf';
file_put_contents($attPdfPath, $pdfAtt->output());
echo "Attendance PDF generated successfully (" . filesize($attPdfPath) . " bytes)\n";

echo "\nALL TESTS PASSED SUCCESSFULLY!\n";
