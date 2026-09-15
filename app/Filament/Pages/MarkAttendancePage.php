<?php

namespace App\Filament\Pages;

use App\Models\AttendanceLog;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarkAttendancePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Attendance Register';
    protected static ?string $slug = 'mark-attendance';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.mark-attendance-page';

    public static function getNavigationLabel(): string
    {
        $user = auth()->user();
        if ($user?->isCoach()) {
            return 'Mark Training Attendance';
        } elseif ($user?->isProjectOfficer()) {
            return 'Mark Session Attendance';
        }
        return 'Live Session Attendance';
    }

    public string $sessionDate = '';
    public string $sessionTime = '09:00 - 11:30';
    public string $sessionTitle = '';
    public string $scope = 'all';
    public ?string $selectedProjectId = '';
    public ?string $selectedTeamId = '';
    public string $searchQuery = '';
    public string $statusFilter = 'all'; // all | present | absent | late | apology

    /**
     * beneficiaryId => 'present' | 'absent' | 'late' | 'apology'
     */
    public array $attendanceStatuses = [];

    /**
     * beneficiaryId => optional note string
     */
    public array $attendanceNotes = [];

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
        $this->sessionDate = now()->toDateString();
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

        $this->updateDefaultSessionTitle();
        $this->loadAttendanceRoster();
    }

    public function updatedSessionDate(): void
    {
        $this->updateDefaultSessionTitle();
        $this->loadAttendanceRoster();
    }

    public function updatedSelectedProjectId(): void
    {
        $user = auth()->user();
        if ($user?->isCoach()) {
            $team = Team::where('coach_id', $user->id)->first();
            $this->selectedProjectId = $team ? (string) $team->project_id : $this->selectedProjectId;
        }
        if (!$user?->isCoach()) {
            $this->selectedTeamId = '';
        }
        $this->updateDefaultSessionTitle();
        $this->loadAttendanceRoster();
    }

    public function updatedSelectedTeamId(): void
    {
        $this->updateDefaultSessionTitle();
        $this->loadAttendanceRoster();
    }

    public function updatedScope(): void
    {
        $user = auth()->user();
        if (!$user?->isProjectOfficer() && !$user?->isCoach()) {
            $this->selectedProjectId = '';
            $this->selectedTeamId    = '';
        }
        $this->updateDefaultSessionTitle();
        $this->loadAttendanceRoster();
    }

    protected function updateDefaultSessionTitle(): void
    {
        $project = $this->selectedProjectId ? Project::find($this->selectedProjectId) : null;
        $dateFormatted = Carbon::parse($this->sessionDate)->format('M d');
        if ($project && $project->programme_type === Project::PROGRAMME_FOOTBALL) {
            $this->sessionTitle = "Session ({$dateFormatted}): Tactical Drills, Physical Training & Match Practice";
        } else {
            $this->sessionTitle = "Session ({$dateFormatted}): Literacy Module Recap, Guided Reading & Evaluation";
        }
    }

    public function syncSessionRoster(): void
    {
        $this->loadAttendanceRoster();
        Notification::make()
            ->title('Session Roster Synced')
            ->body('Roster refreshed from enrolled beneficiary database.')
            ->success()
            ->duration(2000)
            ->send();
    }

    public function loadAttendanceRoster(): void
    {
        $viewData = $this->getViewData();
        $beneficiaries = $viewData['beneficiaries'];
        $beneficiaryIds = $beneficiaries->pluck('id')->toArray();

        // Query existing attendance logs for this session date
        $existingLogs = AttendanceLog::whereIn('beneficiary_id', $beneficiaryIds)
            ->whereDate('attended_at', $this->sessionDate)
            ->get()
            ->keyBy('beneficiary_id');

        $this->attendanceStatuses = [];
        $this->attendanceNotes = [];

        foreach ($beneficiaryIds as $id) {
            if (isset($existingLogs[$id])) {
                $log = $existingLogs[$id];
                $this->attendanceStatuses[$id] = in_array($log->status, ['present', 'absent', 'late', 'apology']) ? $log->status : 'present';
                $this->attendanceNotes[$id] = $log->notes ?? '';
            } else {
                // Default new session marking to 'present' or 'absent'
                $this->attendanceStatuses[$id] = 'present';
                $this->attendanceNotes[$id] = '';
            }
        }
    }

    public function canUserMark(): bool
    {
        $user = auth()->user();
        return (bool) ($user && ($user->isCoach() || $user->isProjectOfficer()));
    }

    public function setStatus(int $beneficiaryId, string $status): void
    {
        if (!$this->canUserMark()) {
            Notification::make()
                ->title('Permission Denied')
                ->body('Register marking is restricted to Coaches and Project Officers. Administrators have read-only monitoring access.')
                ->warning()
                ->send();
            return;
        }

        if (in_array($status, ['present', 'absent', 'late', 'apology'])) {
            $this->attendanceStatuses[$beneficiaryId] = $status;
        }
    }

    public function setStatusFilter(string $filter): void
    {
        $this->statusFilter = $filter;
    }

    public function markAll(string $status): void
    {
        if (!$this->canUserMark()) {
            Notification::make()
                ->title('Permission Denied')
                ->body('Register marking is restricted to Coaches and Project Officers. Administrators have read-only monitoring access.')
                ->warning()
                ->send();
            return;
        }

        if (!in_array($status, ['present', 'absent', 'late', 'apology'])) {
            return;
        }

        foreach ($this->attendanceStatuses as $id => $val) {
            $this->attendanceStatuses[$id] = $status;
        }

        Notification::make()
            ->title("Marked all as " . ucfirst($status))
            ->success()
            ->duration(2000)
            ->send();
    }

    public function saveAttendance(): void
    {
        if (!$this->canUserMark()) {
            Notification::make()
                ->title('Permission Denied')
                ->body('Register marking is restricted to Coaches and Project Officers. Administrators have read-only monitoring access.')
                ->warning()
                ->send();
            return;
        }

        $user = auth()->user();
        $viewData = $this->getViewData();
        $beneficiaries = $viewData['beneficiaries']->keyBy('id');
        $selectedProject = $this->selectedProjectId ? Project::find($this->selectedProjectId) : null;
        $dateStr = Carbon::parse($this->sessionDate)->format('Y-m-d');

        $stats = [
            'present' => 0,
            'absent'  => 0,
            'late'    => 0,
            'apology' => 0,
        ];

        foreach ($this->attendanceStatuses as $beneficiaryId => $status) {
            $beneficiary = $beneficiaries->get($beneficiaryId);
            if (!$beneficiary) continue;

            $projectId = $this->selectedProjectId 
                ?: ($beneficiary->projects()->value('projects.id') ?? ($beneficiary->team?->project_id ?? 1));
            $project = Project::find($projectId);
            $activity = $project?->programme_type === Project::PROGRAMME_FOOTBALL ? AttendanceLog::ACTIVITY_TRAINING : AttendanceLog::ACTIVITY_CLASS_SESSION;
            $note = $this->attendanceNotes[$beneficiaryId] ?? null;

            $existingLog = AttendanceLog::where('beneficiary_id', $beneficiaryId)
                ->where('project_id', $projectId)
                ->whereDate('attended_at', $dateStr)
                ->first();

            if ($existingLog) {
                $existingLog->update([
                    'team_id'             => $beneficiary->team_id,
                    'recorded_by_user_id' => $user->id,
                    'activity_type'       => $activity,
                    'status'              => $status,
                    'notes'               => $note,
                ]);
            } else {
                AttendanceLog::create([
                    'beneficiary_id'      => $beneficiaryId,
                    'project_id'          => $projectId,
                    'attended_at'         => $dateStr,
                    'team_id'             => $beneficiary->team_id,
                    'recorded_by_user_id' => $user->id,
                    'activity_type'       => $activity,
                    'status'              => $status,
                    'notes'               => $note,
                ]);
            }

            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        Notification::make()
            ->title("Attendance Register Saved")
            ->body("{$stats['present']} Present • {$stats['absent']} Absent • {$stats['late']} Late • {$stats['apology']} Apology/Excused for " . Carbon::parse($this->sessionDate)->format('M d, Y'))
            ->success()
            ->send();
    }

    public function getPdfExportUrl(): string
    {
        $user = auth()->user();
        $carbonDate = Carbon::parse($this->sessionDate);
        $projectId = $this->selectedProjectId;
        $teamId = $this->selectedTeamId;
        $scope = $this->scope;

        if ($user?->isCoach()) {
            $coachTeam = Team::where('coach_id', $user->id)->first();
            $projectId = $coachTeam ? (string) $coachTeam->project_id : $projectId;
            $teamId    = $coachTeam ? (string) $coachTeam->id : $teamId;
            $scope     = 'football';
        } elseif ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $projectId = (string) $user->assigned_project_id;
        }

        return route('registers.export.attendance', [
            'month'      => (int) $carbonDate->month,
            'year'       => (int) $carbonDate->year,
            'project_id' => $projectId,
            'team_id'    => $teamId,
            'scope'      => $scope,
        ]);
    }

    public function exportPdf(): StreamedResponse
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $user = auth()->user();
        $carbonDate = Carbon::parse($this->sessionDate);
        $month = (int) $carbonDate->month;
        $year  = (int) $carbonDate->year;

        $projectId = $this->selectedProjectId;
        $teamId    = $this->selectedTeamId;
        $scope     = $this->scope;

        // Role locks for export
        if ($user?->isCoach()) {
            $coachTeam = Team::where('coach_id', $user->id)->first();
            $projectId = $coachTeam ? (string) $coachTeam->project_id : $projectId;
            $teamId    = $coachTeam ? (string) $coachTeam->id : $teamId;
            $scope     = 'football';
        } elseif ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $projectId = (string) $user->assigned_project_id;
        }

        // Build 5-week monthly matrix data for Image 3 layout
        $startOfMonth = $carbonDate->copy()->startOfMonth();
        $endOfMonth   = $carbonDate->copy()->endOfMonth();

        $weeksStructure = [];
        $currentDate = $startOfMonth->copy();
        $weekNum = 1;
        $days = [];

        while ($currentDate->lte($endOfMonth)) {
            if ($currentDate->isWeekday()) {
                $days[] = [
                    'day_number' => $currentDate->day,
                    'day_label'  => match ($currentDate->dayOfWeek) {
                        Carbon::MONDAY    => 'M',
                        Carbon::TUESDAY   => 'T',
                        Carbon::WEDNESDAY => 'W',
                        Carbon::THURSDAY  => 'TH',
                        Carbon::FRIDAY    => 'F',
                    },
                    'full_date'  => $currentDate->toDateString(),
                ];
            }

            if ($currentDate->dayOfWeek === Carbon::FRIDAY || $currentDate->copy()->addDay()->month !== $month) {
                if (!empty($days)) {
                    $weeksStructure[$weekNum] = $days;
                    $weekNum++;
                    $days = [];
                }
            }
            $currentDate->addDay();
        }

        // Fetch logs for this month
        $beneficiaryIds = $beneficiaries->pluck('id');
        $attLogs = AttendanceLog::whereIn('beneficiary_id', $beneficiaryIds)
            ->whereMonth('attended_at', $month)
            ->whereYear('attended_at', $year)
            ->get();

        $matrix = [];
        foreach ($attLogs as $log) {
            $d = Carbon::parse($log->attended_at)->day;
            $matrix[$log->beneficiary_id][$d] = $log->status;
        }

        $isFootball = $project && $project->programme_type === Project::PROGRAMME_FOOTBALL;
        if ($isFootball) {
            $coachUser = null;
            if ($team && $team->coach) {
                $coachUser = $team->coach;
            } elseif ($user->isCoach()) {
                $coachUser = $user;
            } elseif ($firstTeamCoach = Team::where('project_id', $project->id)->whereNotNull('coach_id')->first()?->coach) {
                $coachUser = $firstTeamCoach;
            }
            $instructorName = $coachUser ? $coachUser->name : 'Assigned Coach';
        } else {
            $officerUser = $user->isProjectOfficer() ? $user : User::where('role', User::ROLE_PROJECT_OFFICER)->first();
            $instructorName = $officerUser ? $officerUser->name : $user->name;
        }

        $pdf = Pdf::loadView('pdf.attendance-register-sheet', [
            'project'         => $project,
            'team'            => $team,
            'beneficiaries'   => $beneficiaries,
            'weeksStructure'  => $weeksStructure,
            'matrix'          => $matrix,
            'month'           => $month,
            'year'            => $year,
            'monthName'       => $carbonDate->format('F Y'),
            'instructorName'  => $instructorName,
            'generatedAt'     => now()->format('d M Y, H:i'),
            'docRef'          => 'PIF-REG-' . ($project ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $project->name), 0, 8)) : 'MAIN') . '-' . now()->format('Ymd'),
        ])->setPaper('a4', 'landscape');

        $filename = 'PIF-Attendance-Register-' . ($project ? str_replace(' ', '-', $project->name) : 'All') . '-' . $carbonDate->format('Y-m') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function getViewData(): array
    {
        $user      = auth()->user();
        $isOfficer = (bool) $user?->isProjectOfficer();
        $isCoach   = (bool) $user?->isCoach();
        $canMark   = $this->canUserMark();

        $projectsQuery = Project::where('is_active', true);
        if ($this->scope === Project::PROGRAMME_EDUCATION) {
            $projectsQuery->where('programme_type', Project::PROGRAMME_EDUCATION);
        } elseif ($this->scope === Project::PROGRAMME_FOOTBALL) {
            $projectsQuery->where('programme_type', Project::PROGRAMME_FOOTBALL);
        }
        $projects = $projectsQuery->orderBy('name')->get();

        $selectedProject = $this->selectedProjectId ? Project::find($this->selectedProjectId) : null;
        $teams = collect();
        $isFootballProject = $selectedProject && $selectedProject->programme_type === Project::PROGRAMME_FOOTBALL;

        if ($isFootballProject) {
            $teams = Team::where('project_id', $selectedProject->id)->orderBy('name')->get();
        }

        $benefQuery = Beneficiary::where('is_active', true)
            ->with(['team:id,name', 'projects:id,name,programme_type'])
            ->select(['id', 'name', 'phone_number', 'team_id', 'shortcode']);

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

        if ($this->searchQuery) {
            $benefQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('shortcode', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('phone_number', 'like', '%' . $this->searchQuery . '%');
            });
        }

        $beneficiaries = $benefQuery->orderBy('name')->get();

        // Calculate real-time KPI metrics
        $registeredCount = $beneficiaries->count();
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $apologyCount = 0;

        foreach ($beneficiaries as $b) {
            $status = $this->attendanceStatuses[$b->id] ?? 'present';
            match ($status) {
                'present' => $presentCount++,
                'absent'  => $absentCount++,
                'late'    => $lateCount++,
                'apology' => $apologyCount++,
                default   => null,
            };
        }

        $attendanceRate = $registeredCount > 0 ? round((($presentCount + $lateCount) / $registeredCount) * 100) : 0;

        // Session Coach (Football) vs Instructor (Literacy)
        if ($isFootballProject) {
            $coachUser = null;
            if ($isCoach) {
                $coachUser = $user;
            } elseif ($this->selectedTeamId) {
                $t = $teams->firstWhere('id', $this->selectedTeamId);
                $coachUser = $t?->coach;
            } elseif ($firstTeamCoach = Team::where('project_id', $selectedProject?->id ?? 6)->whereNotNull('coach_id')->first()?->coach) {
                $coachUser = $firstTeamCoach;
            }
            $instructorTitle = 'Coach';
            $instructorName = $coachUser ? $coachUser->name : ($user->isCoach() ? $user->name : 'Assigned Coach');
        } else {
            $officerUser = $isOfficer ? $user : User::where('role', User::ROLE_PROJECT_OFFICER)->first();
            $instructorTitle = 'Instructor';
            $instructorName = $officerUser ? $officerUser->name : $user->name;
        }

        // Active Session Badge codes & Group labels (Team for Football, Class for Literacy)
        $courseCode = $selectedProject ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $selectedProject->name), 0, 7)) . '-PIF' : 'PIF-PROG';
        $groupType = $isFootballProject ? 'Team' : 'Class';

        if ($isCoach) {
            $groupName = $coachTeam?->name ?? 'Senior Squad';
        } elseif ($this->selectedTeamId && $isFootballProject) {
            $groupName = $teams->firstWhere('id', $this->selectedTeamId)?->name ?? 'All Teams';
        } elseif ($selectedProject) {
            $groupName = $selectedProject->name;
        } else {
            $groupName = $isFootballProject ? 'All Teams' : 'All Classes';
        }

        $groupBadge = "{$groupType}: {$groupName}";
        $percentOfLabel = $isFootballProject ? 'of team' : 'of class';

        return [
            'projects'            => $projects,
            'teams'               => $teams,
            'beneficiaries'       => $beneficiaries,
            'selectedProject'     => $selectedProject,
            'isProjectOfficer'    => $isOfficer,
            'isCoach'             => $isCoach,
            'canMark'             => $canMark,
            'lockScope'           => $isOfficer || $isCoach,
            'lockProject'         => $isOfficer || $isCoach,
            'showTeamFilter'      => $isFootballProject && !$isCoach,
            'registeredCount'     => $registeredCount,
            'presentCount'        => $presentCount,
            'absentCount'         => $absentCount,
            'lateCount'           => $lateCount,
            'apologyCount'        => $apologyCount,
            'attendanceRate'      => $attendanceRate,
            'instructorTitle'     => $instructorTitle,
            'instructorName'      => $instructorName,
            'courseCode'          => $courseCode,
            'groupType'           => $groupType,
            'groupName'           => $groupName,
            'groupBadge'          => $groupBadge,
            'percentOfLabel'      => $percentOfLabel,
            'isFootballProject'   => $isFootballProject,
        ];
    }
}
