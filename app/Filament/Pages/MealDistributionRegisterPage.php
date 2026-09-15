<?php

namespace App\Filament\Pages;

use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MealDistributionRegisterPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Meal Distribution Register (Cooks)';
    protected static ?string $slug = 'meal-distribution-register';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.meal-distribution-register-page';

    public static function getNavigationLabel(): string
    {
        return 'Meal Distribution Register';
    }

    public string  $scope             = 'all'; // all | education | football
    public ?string $selectedProjectId = '';
    public ?string $selectedTeamId    = '';
    public int     $filterMonth;
    public int     $filterYear;
    public array   $weeksStructure   = [];

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            User::ROLE_HEAD_OF_PROGRAMMES,
            User::ROLE_SYSTEM_MANAGER,
            User::ROLE_PROJECT_OFFICER,
        ]);
    }

    public function getHeading(): string
    {
        $monthName = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->format('F Y');
        return "Meal Distribution Register (Cooks) — {$monthName}";
    }

    public function getSubheading(): ?string
    {
        $monthName = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->format('F Y');
        return "Daily feeding records strictly fed from Kitchen Cook Terminal distributions for {$monthName}";
    }

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $this->selectedProjectId = (string) $user->assigned_project_id;
            $project = Project::find($user->assigned_project_id);
            $this->scope = $project?->programme_type ?? 'all';
        } else {
            $first = Project::where('is_active', true)->orderBy('name')->first();
            $this->selectedProjectId = $first ? (string) $first->id : '';
        }

        $this->filterMonth = (int) now()->month;
        $this->filterYear  = (int) now()->year;

        $this->buildSmartCalendarStructure();
    }

    public function updatedScope(): void
    {
        $user = auth()->user();
        if (!$user?->isProjectOfficer()) {
            $this->selectedProjectId = '';
            $this->selectedTeamId    = '';
        }
        $this->buildSmartCalendarStructure();
    }

    public function updatedSelectedProjectId(): void
    {
        $this->selectedTeamId = '';
        $this->buildSmartCalendarStructure();
    }

    public function updatedSelectedTeamId(): void
    {
        $this->buildSmartCalendarStructure();
    }

    public function updatedFilterMonth(): void { $this->buildSmartCalendarStructure(); }
    public function updatedFilterYear(): void  { $this->buildSmartCalendarStructure(); }

    public function getPdfExportUrl(): string
    {
        $user = auth()->user();
        $projectId = $this->selectedProjectId;
        $teamId    = $this->selectedTeamId;
        $scope     = $this->scope;

        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $projectId = (string) $user->assigned_project_id;
        }

        return route('registers.export.meals', [
            'month'      => $this->filterMonth,
            'year'       => $this->filterYear,
            'project_id' => $projectId,
            'team_id'    => $teamId,
            'scope'      => $scope,
        ]);
    }

    public function exportPdf(): StreamedResponse
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        if (empty($this->weeksStructure)) {
            $this->buildSmartCalendarStructure();
        }

        $user = auth()->user();
        $viewData = $this->getViewData();
        $beneficiaries = $viewData['beneficiaries'];

        $projectId = $this->selectedProjectId;
        $teamId    = $this->selectedTeamId;

        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $projectId = (string) $user->assigned_project_id;
        }

        $project = $projectId ? Project::find($projectId) : Project::first();
        $team = $teamId ? Team::find($teamId) : null;

        $carbonDate = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1);
        $beneficiaryIds = $beneficiaries->pluck('id');

        $mealLogs = MealLog::whereIn('beneficiary_id', $beneficiaryIds)
            ->whereMonth('served_at', $this->filterMonth)
            ->whereYear('served_at', $this->filterYear)
            ->get();

        $matrix = [];
        foreach ($mealLogs as $log) {
            $d = Carbon::parse($log->served_at)->day;
            $matrix[$log->beneficiary_id][$d] = ($matrix[$log->beneficiary_id][$d] ?? 0) + 1;
        }

        if ($team && !$project) {
            $project = $team->project;
        }

        // Identify cook terminal operators who recorded the meals
        $cookIds = $mealLogs->pluck('served_by_user_id')->unique()->filter();
        $cooks = User::whereIn('id', $cookIds)->pluck('name')->toArray();
        $cooksLabel = !empty($cooks) ? implode(', ', $cooks) : 'Kitchen Cook Terminal';

        $pdf = Pdf::loadView('pdf.meal-distribution-register-sheet', [
            'project'         => $project,
            'team'            => $team,
            'beneficiaries'   => $beneficiaries,
            'weeksStructure'  => $this->weeksStructure,
            'matrix'          => $matrix,
            'month'           => $this->filterMonth,
            'year'            => $this->filterYear,
            'monthName'       => $carbonDate->format('F Y'),
            'cooksLabel'      => $cooksLabel,
            'totalMeals'      => $mealLogs->count(),
            'generatedAt'     => now()->format('d M Y, H:i'),
            'docRef'          => 'PIF-MEAL-' . ($project ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $project->name), 0, 8)) : 'MAIN') . '-' . now()->format('Ymd'),
        ])->setPaper('a4', 'landscape');

        $filename = 'PIF-Meal-Distribution-Register-' . ($project ? str_replace(' ', '-', $project->name) : 'All') . '-' . $carbonDate->format('Y-m') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    protected function buildSmartCalendarStructure(): void
    {
        $startOfMonth = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $this->weeksStructure  = [];
        $currentDate           = $startOfMonth->copy();
        $currentWeekNumber     = 1;
        $weekDaysCollection    = [];

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

            if ($currentDate->dayOfWeek === Carbon::FRIDAY || $currentDate->copy()->addDay()->month !== $this->filterMonth) {
                if (!empty($weekDaysCollection)) {
                    $this->weeksStructure[$currentWeekNumber] = $weekDaysCollection;
                    $currentWeekNumber++;
                    $weekDaysCollection = [];
                }
            }

            $currentDate->addDay();
        }
    }

    protected function getViewData(): array
    {
        $user      = auth()->user();
        $isOfficer = $user?->isProjectOfficer();

        // -- Projects dropdown (filtered by scope) ----------------------------
        $projectsQuery = Project::where('is_active', true);
        if ($this->scope === Project::PROGRAMME_EDUCATION) {
            $projectsQuery->where('programme_type', Project::PROGRAMME_EDUCATION);
        } elseif ($this->scope === Project::PROGRAMME_FOOTBALL) {
            $projectsQuery->where('programme_type', Project::PROGRAMME_FOOTBALL);
        }
        $projects = $projectsQuery->orderBy('name')->get();

        // -- Teams dropdown (football projects only) ---------------------------
        $selectedProject = $this->selectedProjectId ? Project::find($this->selectedProjectId) : null;
        $teams           = collect();
        $isFootballProject = $selectedProject && $selectedProject->programme_type === Project::PROGRAMME_FOOTBALL;

        if ($isFootballProject) {
            $teams = Team::where('project_id', $selectedProject->id)->orderBy('name')->get();
        }

        // -- Beneficiary scope -------------------------------------------------
        $benefQuery = Beneficiary::where('is_active', true)
            ->with(['team:id,name', 'projects:id,name,programme_type'])
            ->select(['id', 'name', 'phone_number', 'team_id', 'shortcode']);

        if ($this->selectedTeamId) {
            $benefQuery->where('team_id', $this->selectedTeamId);
        } elseif ($this->selectedProjectId) {
            $benefQuery->inProject((int) $this->selectedProjectId);
        } elseif ($this->scope === Project::PROGRAMME_EDUCATION) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_EDUCATION));
        } elseif ($this->scope === Project::PROGRAMME_FOOTBALL) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_FOOTBALL));
        }

        $beneficiaries  = $benefQuery->orderBy('name')->get();
        $beneficiaryIds = $beneficiaries->pluck('id');

        // -- Meal Distribution Matrix (Fed directly from Cook Terminal) --------
        $mealMatrix  = [];
        $dailyTotals = [];
        $totalMealsCount = 0;
        $fedBeneficiaryIds = [];

        if ($beneficiaryIds->isNotEmpty()) {
            $logs = MealLog::whereIn('beneficiary_id', $beneficiaryIds)
                ->whereMonth('served_at', $this->filterMonth)
                ->whereYear('served_at', $this->filterYear)
                ->select(['beneficiary_id', 'served_at'])
                ->get();

            $totalMealsCount = $logs->count();

            foreach ($logs as $log) {
                $day = Carbon::parse($log->served_at)->day;
                $mealMatrix[$log->beneficiary_id][$day] = ($mealMatrix[$log->beneficiary_id][$day] ?? 0) + 1;
                $dailyTotals[$day] = ($dailyTotals[$day] ?? 0) + 1;
                $fedBeneficiaryIds[$log->beneficiary_id] = true;
            }
        }

        // -- Register label ----------------------------------------------------
        if ($this->selectedTeamId && $isFootballProject) {
            $teamName      = $teams->firstWhere('id', $this->selectedTeamId)?->name ?? '-';
            $registerLabel = 'Football Team: ' . $teamName;
        } elseif ($selectedProject) {
            $registerLabel = 'Project: ' . $selectedProject->name;
        } elseif ($this->scope === Project::PROGRAMME_EDUCATION) {
            $registerLabel = 'All Education Beneficiaries';
        } elseif ($this->scope === Project::PROGRAMME_FOOTBALL) {
            $registerLabel = 'All Football Beneficiaries';
        } else {
            $registerLabel = 'All Beneficiaries';
        }

        $monthCarbon = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1);

        return [
            'projects'              => $projects,
            'teams'                 => $teams,
            'beneficiaries'         => $beneficiaries,
            'mealMatrix'            => $mealMatrix,
            'dailyTotals'           => $dailyTotals,
            'totalMealsCount'       => $totalMealsCount,
            'uniqueBeneficiariesFed'=> count($fedBeneficiaryIds),
            'registerLabel'         => $registerLabel,
            'selectedMonthName'     => $monthCarbon->format('F Y'),
            'selectedMonthShort'    => $monthCarbon->format('M Y'),
            'isProjectOfficer'      => $isOfficer,
            'lockScope'             => (bool) $isOfficer,
            'lockProject'           => (bool) $isOfficer,
            'showTeamFilter'        => $isFootballProject,
            'monthsList'            => [
                1 => 'January',   2 => 'February', 3 => 'March',     4 => 'April',
                5 => 'May',       6 => 'June',     7 => 'July',      8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ],
            'yearsList' => range(now()->year - 1, now()->year + 2),
        ];
    }
}
