<?php

namespace App\Filament\Pages;

use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;

class ProjectRegistersPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Monthly Attendance Registers';
    protected static ?string $slug = 'project-registers';
    protected static string $view = 'filament.pages.project-registers-page';

    public string  $scope           = 'all'; // all | education | football
    public ?string $selectedProjectId = '';
    public ?string $selectedTeamId    = '';
    public int     $filterMonth;
    public int     $filterYear;
    public array   $weeksStructure  = [];

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            User::ROLE_HEAD_OF_PROGRAMMES,
            User::ROLE_SYSTEM_MANAGER,
            User::ROLE_PROJECT_OFFICER,
            User::ROLE_COACH,
        ]);
    }

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $this->selectedProjectId = (string) $user->assigned_project_id;
            $project = Project::find($user->assigned_project_id);
            $this->scope = $project?->programme_type ?? 'all';
        } elseif ($user?->isCoach()) {
            $team = Team::where('coach_id', $user->id)->first();
            $this->selectedProjectId = $team ? (string) $team->project_id : '';
            $this->selectedTeamId    = $team ? (string) $team->id : '';
            $this->scope = 'football';
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
        if (!$user?->isProjectOfficer() && !$user?->isCoach()) {
            $this->selectedProjectId = '';
            $this->selectedTeamId    = '';
        }
        $this->buildSmartCalendarStructure();
    }

    public function updatedSelectedProjectId(): void
    {
        $user = auth()->user();
        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $this->selectedProjectId = (string) $user->assigned_project_id;
        } elseif ($user?->isCoach()) {
            $team = Team::where('coach_id', $user->id)->first();
            $this->selectedProjectId = $team ? (string) $team->project_id : $this->selectedProjectId;
        }
        if (!$user?->isCoach()) {
            $this->selectedTeamId = '';
        }
        $this->buildSmartCalendarStructure();
    }

    public function updatedSelectedTeamId(): void
    {
        $this->buildSmartCalendarStructure();
    }

    public function updatedFilterMonth(): void { $this->buildSmartCalendarStructure(); }
    public function updatedFilterYear(): void  { $this->buildSmartCalendarStructure(); }

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
                        Carbon::THURSDAY  => 'T',
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
        $isCoach   = $user?->isCoach();

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
        $benefQuery = Beneficiary::where('is_active', true)->select(['id', 'name', 'team_id']);

        if ($isCoach) {
            $coachTeam = Team::where('coach_id', $user->id)->first();
            $benefQuery->where('team_id', $coachTeam?->id ?? 0);
        } elseif ($this->selectedTeamId) {
            $benefQuery->where('team_id', $this->selectedTeamId);
        } elseif ($this->selectedProjectId) {
            $benefQuery->inProject((int) $this->selectedProjectId);
        } elseif ($this->scope === Project::PROGRAMME_EDUCATION) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_EDUCATION));
        } elseif ($this->scope === Project::PROGRAMME_FOOTBALL) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_FOOTBALL));
        }
        // scope='all' + no project ? all active beneficiaries

        $beneficiaries  = $benefQuery->orderBy('name')->get();
        $beneficiaryIds = $beneficiaries->pluck('id');

        // -- Meal matrix -------------------------------------------------------
        $mealMatrix = [];
        if ($beneficiaryIds->isNotEmpty()) {
            $logs = MealLog::whereIn('beneficiary_id', $beneficiaryIds)
                ->whereMonth('served_at', $this->filterMonth)
                ->whereYear('served_at', $this->filterYear)
                ->select(['beneficiary_id', 'served_at'])
                ->toBase()
                ->get();

            foreach ($logs as $log) {
                $day = Carbon::parse($log->served_at)->day;
                $mealMatrix[$log->beneficiary_id][$day] = true;
            }
        }

        // -- Register label ----------------------------------------------------
        if ($isCoach) {
            $coachTeamName = Team::where('coach_id', $user->id)->value('name') ?? '—';
            $registerLabel = 'Team: ' . $coachTeamName;
        } elseif ($this->selectedTeamId && $isFootballProject) {
            $teamName      = $teams->firstWhere('id', $this->selectedTeamId)?->name ?? '—';
            $registerLabel = 'Team: ' . $teamName;
        } elseif ($selectedProject) {
            $registerLabel = 'Project: ' . $selectedProject->name;
        } elseif ($this->scope === Project::PROGRAMME_EDUCATION) {
            $registerLabel = 'All Education Beneficiaries';
        } elseif ($this->scope === Project::PROGRAMME_FOOTBALL) {
            $registerLabel = 'All Football Beneficiaries';
        } else {
            $registerLabel = 'All Beneficiaries';
        }

        return [
            'projects'         => $projects,
            'teams'            => $teams,
            'beneficiaries'    => $beneficiaries,
            'mealMatrix'       => $mealMatrix,
            'registerLabel'    => $registerLabel,
            'isProjectOfficer' => $isOfficer,
            'isCoach'          => $isCoach,
            'lockScope'        => $isOfficer || $isCoach,
            'lockProject'      => $isOfficer || $isCoach,
            'showTeamFilter'   => $isFootballProject && !$isCoach,
            'monthsList'       => [
                1 => 'Jan',  2 => 'Feb',  3 => 'Mar',  4 => 'Apr',
                5 => 'May',  6 => 'Jun',  7 => 'Jul',  8 => 'Aug',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ],
            'yearsList' => range(now()->year - 1, now()->year + 2),
        ];
    }
}
