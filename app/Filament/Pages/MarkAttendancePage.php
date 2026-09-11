<?php

namespace App\Filament\Pages;

use App\Models\AttendanceLog;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MarkAttendancePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Mark Attendance';
    protected static ?string $slug = 'mark-attendance';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.mark-attendance-page';

    public string $sessionDate = '';
    public string $scope = 'all';
    public ?string $selectedProjectId = '';
    public ?string $selectedTeamId = '';
    public string $searchQuery = '';
    public array $attendanceStates = []; // beneficiaryId => bool

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

        $this->loadAttendanceRoster();
    }

    public function updatedSessionDate(): void
    {
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
        $this->loadAttendanceRoster();
    }

    public function updatedSelectedTeamId(): void
    {
        $this->loadAttendanceRoster();
    }

    public function updatedScope(): void
    {
        $user = auth()->user();
        if (!$user?->isProjectOfficer() && !$user?->isCoach()) {
            $this->selectedProjectId = '';
            $this->selectedTeamId    = '';
        }
        $this->loadAttendanceRoster();
    }

    public function loadAttendanceRoster(): void
    {
        $viewData = $this->getViewData();
        $beneficiaries = $viewData['beneficiaries'];
        $beneficiaryIds = $beneficiaries->pluck('id')->toArray();

        // Query already logged attendance for this specific date
        $alreadyPresent = AttendanceLog::whereIn('beneficiary_id', $beneficiaryIds)
            ->whereDate('attended_at', $this->sessionDate)
            ->where('status', 'present')
            ->pluck('beneficiary_id')
            ->toArray();

        $this->attendanceStates = [];
        foreach ($beneficiaryIds as $id) {
            $this->attendanceStates[$id] = in_array($id, $alreadyPresent);
        }
    }

    public function toggleBeneficiary(int $id): void
    {
        if (isset($this->attendanceStates[$id])) {
            $this->attendanceStates[$id] = !$this->attendanceStates[$id];
        } else {
            $this->attendanceStates[$id] = true;
        }
    }

    public function markAllPresent(): void
    {
        foreach ($this->attendanceStates as $id => $val) {
            $this->attendanceStates[$id] = true;
        }
    }

    public function markAllAbsent(): void
    {
        foreach ($this->attendanceStates as $id => $val) {
            $this->attendanceStates[$id] = false;
        }
    }

    public function saveAttendance(): void
    {
        $user = auth()->user();
        $viewData = $this->getViewData();
        $beneficiaries = $viewData['beneficiaries']->keyBy('id');
        $selectedProject = $this->selectedProjectId ? Project::find($this->selectedProjectId) : null;
        $isFootball = $selectedProject && $selectedProject->programme_type === Project::PROGRAMME_FOOTBALL;
        $defaultActivity = $isFootball ? AttendanceLog::ACTIVITY_TRAINING : AttendanceLog::ACTIVITY_CLASS_SESSION;

        $presentCount = 0;
        $totalCount = count($this->attendanceStates);

        foreach ($this->attendanceStates as $beneficiaryId => $isPresent) {
            $beneficiary = $beneficiaries->get($beneficiaryId);
            if (!$beneficiary) continue;

            $projectId = $this->selectedProjectId ?: ($beneficiary->projects()->value('projects.id') ?? 1);
            $project = Project::find($projectId);
            $activity = $project?->programme_type === Project::PROGRAMME_FOOTBALL ? AttendanceLog::ACTIVITY_TRAINING : AttendanceLog::ACTIVITY_CLASS_SESSION;

            $existing = AttendanceLog::where('beneficiary_id', $beneficiaryId)
                ->whereDate('attended_at', $this->sessionDate)
                ->first();

            if ($isPresent) {
                if ($existing) {
                    $existing->update([
                        'status'              => 'present',
                        'recorded_by_user_id' => $user->id,
                        'activity_type'       => $activity,
                        'project_id'          => $projectId,
                        'team_id'             => $beneficiary->team_id,
                    ]);
                } else {
                    AttendanceLog::create([
                        'beneficiary_id'      => $beneficiaryId,
                        'project_id'          => $projectId,
                        'team_id'             => $beneficiary->team_id,
                        'recorded_by_user_id' => $user->id,
                        'activity_type'       => $activity,
                        'attended_at'         => $this->sessionDate,
                        'status'              => 'present',
                    ]);
                }
                $presentCount++;
            } else {
                if ($existing) {
                    $existing->delete();
                }
            }
        }

        Notification::make()
            ->title("Attendance Saved: {$presentCount} / {$totalCount} Present")
            ->body("Recorded for " . Carbon::parse($this->sessionDate)->format('l, F j, Y'))
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $user      = auth()->user();
        $isOfficer = $user?->isProjectOfficer();
        $isCoach   = $user?->isCoach();

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

        if ($this->searchQuery) {
            $benefQuery->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        $beneficiaries = $benefQuery->orderBy('name')->get();

        // Activity banner text & icon
        if ($isCoach || $isFootballProject) {
            $activityTitle = 'Football Training Attendance';
            $activityDescription = 'Record daily training session attendance for football players and squad members.';
            $activityTag = 'Training Session';
            $activityIcon = 'football';
        } else {
            $activityTitle = 'Literacy / Education Session Attendance';
            $activityDescription = 'Record class & session attendance for literacy and educational modules.';
            $activityTag = 'Class / Session';
            $activityIcon = 'book';
        }

        return [
            'projects'            => $projects,
            'teams'               => $teams,
            'beneficiaries'       => $beneficiaries,
            'isProjectOfficer'    => $isOfficer,
            'isCoach'             => $isCoach,
            'lockScope'           => $isOfficer || $isCoach,
            'lockProject'         => $isOfficer || $isCoach,
            'showTeamFilter'      => $isFootballProject && !$isCoach,
            'activityTitle'       => $activityTitle,
            'activityDescription' => $activityDescription,
            'activityTag'         => $activityTag,
            'activityIcon'        => $activityIcon,
        ];
    }
}
