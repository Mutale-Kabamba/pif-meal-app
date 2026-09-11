<?php

namespace App\Filament\Pages;

use App\Models\AttendanceLog;
use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ProjectRegistersPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Monthly Attendance Registers';
    protected static ?string $slug = 'project-registers';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.project-registers-page';

    public string  $registerType     = 'attendance'; // attendance | meals
    public string  $scope            = 'all'; // all | education | football
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

    public function getHeading(): string
    {
        $monthName = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->format('F Y');
        $label = $this->registerType === 'attendance' ? 'Monthly Attendance Registers' : 'Monthly Meal Registers';
        return "{$label} — {$monthName}";
    }

    public function getSubheading(): ?string
    {
        $monthName = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->format('F Y');
        if ($this->registerType === 'attendance') {
            return "Football training & literacy class session attendance marked by coaches and project officers for {$monthName}";
        }
        return "Meal distribution logs served by kitchen cooks for {$monthName}";
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

    public function toggleAttendance(int $beneficiaryId, int $day): void
    {
        // Only allow toggling when on attendance register mode
        if ($this->registerType !== 'attendance') {
            return;
        }

        $user = auth()->user();
        $date = Carbon::createFromDate($this->filterYear, $this->filterMonth, $day)->toDateString();
        $beneficiary = Beneficiary::find($beneficiaryId);
        if (!$beneficiary) return;

        $existing = AttendanceLog::where('beneficiary_id', $beneficiaryId)
            ->whereDate('attended_at', $date)
            ->first();

        if ($existing) {
            $existing->delete();
            Notification::make()
                ->title("Marked absent: {$beneficiary->name} on " . Carbon::parse($date)->format('M j'))
                ->info()
                ->duration(2000)
                ->send();
        } else {
            $projectId = $this->selectedProjectId ?: ($beneficiary->projects()->value('projects.id') ?? 1);
            $project = Project::find($projectId);
            $activity = $project?->programme_type === Project::PROGRAMME_FOOTBALL 
                ? AttendanceLog::ACTIVITY_TRAINING 
                : AttendanceLog::ACTIVITY_CLASS_SESSION;

            AttendanceLog::create([
                'beneficiary_id'      => $beneficiaryId,
                'project_id'          => $projectId,
                'team_id'             => $beneficiary->team_id,
                'recorded_by_user_id' => $user->id,
                'activity_type'       => $activity,
                'attended_at'         => $date,
                'status'              => 'present',
            ]);

            Notification::make()
                ->title("Marked present: {$beneficiary->name} on " . Carbon::parse($date)->format('M j'))
                ->success()
                ->duration(2000)
                ->send();
        }
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

        $beneficiaries  = $benefQuery->orderBy('name')->get();
        $beneficiaryIds = $beneficiaries->pluck('id');

        // -- Attendance Matrix (Marked by Coaches / Project Officers) ----------
        $attendanceMatrix = [];
        if ($beneficiaryIds->isNotEmpty()) {
            $attLogs = AttendanceLog::whereIn('beneficiary_id', $beneficiaryIds)
                ->whereMonth('attended_at', $this->filterMonth)
                ->whereYear('attended_at', $this->filterYear)
                ->where('status', 'present')
                ->select(['beneficiary_id', 'attended_at'])
                ->toBase()
                ->get();

            foreach ($attLogs as $log) {
                $day = Carbon::parse($log->attended_at)->day;
                $attendanceMatrix[$log->beneficiary_id][$day] = true;
            }
        }

        // -- Meal matrix (Marked by Cooks) -------------------------------------
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

        // -- Activity & Register label -----------------------------------------
        if ($isCoach) {
            $coachTeamName = Team::where('coach_id', $user->id)->value('name') ?? '-';
            $activityLabel = 'Football Training';
            $registerLabel = 'Football Team: ' . $coachTeamName;
        } elseif ($this->selectedTeamId && $isFootballProject) {
            $teamName      = $teams->firstWhere('id', $this->selectedTeamId)?->name ?? '-';
            $activityLabel = 'Football Training';
            $registerLabel = 'Football Team: ' . $teamName;
        } elseif ($selectedProject) {
            $activityLabel = $isFootballProject ? 'Football Training' : 'Literacy Class / Session';
            $registerLabel = 'Project: ' . $selectedProject->name;
        } elseif ($this->scope === Project::PROGRAMME_EDUCATION) {
            $activityLabel = 'Literacy / Education Class Sessions';
            $registerLabel = 'All Education Beneficiaries';
        } elseif ($this->scope === Project::PROGRAMME_FOOTBALL) {
            $activityLabel = 'Football Training Sessions';
            $registerLabel = 'All Football Beneficiaries';
        } else {
            $activityLabel = 'Training & Class Sessions';
            $registerLabel = 'All Beneficiaries';
        }

        $monthCarbon = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1);

        return [
            'projects'           => $projects,
            'teams'              => $teams,
            'beneficiaries'      => $beneficiaries,
            'attendanceMatrix'   => $attendanceMatrix,
            'mealMatrix'         => $mealMatrix,
            'activeMatrix'       => $this->registerType === 'attendance' ? $attendanceMatrix : $mealMatrix,
            'activityLabel'      => $activityLabel,
            'registerLabel'      => $registerLabel,
            'selectedMonthName'  => $monthCarbon->format('F Y'),
            'selectedMonthShort' => $monthCarbon->format('M Y'),
            'isProjectOfficer'   => $isOfficer,
            'isCoach'            => $isCoach,
            'lockScope'          => $isOfficer || $isCoach,
            'lockProject'        => $isOfficer || $isCoach,
            'showTeamFilter'     => $isFootballProject && !$isCoach,
            'monthsList'         => [
                1 => 'January',   2 => 'February', 3 => 'March',     4 => 'April',
                5 => 'May',       6 => 'June',     7 => 'July',      8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ],
            'yearsList' => range(now()->year - 1, now()->year + 2),
        ];
    }
}
