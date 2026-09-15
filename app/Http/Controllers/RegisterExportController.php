<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegisterExportController extends Controller
{
    /**
     * Export the Attendance Register (Training / Classes) PDF.
     */
    public function exportAttendance(Request $request): StreamedResponse
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $user      = auth()->user();
        abort_unless($user && ($user->isHeadOfProgrammes() || $user->isSystemManager() || $user->isProjectOfficer() || $user->isCoach()), 403, 'Unauthorized export request.');

        $isCoach   = $user->isCoach();
        $isOfficer = $user->isProjectOfficer();

        $month = (int) ($request->query('month') ?: now()->month);
        $year  = (int) ($request->query('year') ?: now()->year);
        $scope = $request->query('scope', 'all');

        $projectId = $request->query('project_id');
        $teamId    = $request->query('team_id');

        // Enforce strict role-based export scoping
        if ($isCoach) {
            $coachTeam = Team::where('coach_id', $user->id)->first();
            if ($teamId && $coachTeam && (int) $teamId !== (int) $coachTeam->id) {
                abort(403, 'Unauthorized: Coaches can only export registers for their assigned team.');
            }
            $teamId    = $coachTeam ? (string) $coachTeam->id : '0';
            $projectId = $coachTeam ? (string) $coachTeam->project_id : null;
            $scope     = 'football';
        } elseif ($isOfficer && $user->assigned_project_id) {
            if ($projectId && (int) $projectId !== (int) $user->assigned_project_id) {
                abort(403, 'Unauthorized: Project Officers can only export registers for their assigned project.');
            }
            $projectId = (string) $user->assigned_project_id;
        }

        $project = $projectId ? Project::find($projectId) : Project::first();
        $team    = $teamId ? Team::find($teamId) : null;

        // Build Beneficiaries Query
        $benefQuery = Beneficiary::where('is_active', true)
            ->with(['team:id,name', 'projects:id,name,programme_type'])
            ->select(['id', 'name', 'phone_number', 'team_id', 'shortcode']);

        if ($isCoach) {
            $benefQuery->where('team_id', $teamId ?: 0);
        } elseif ($teamId) {
            $benefQuery->where('team_id', $teamId);
        } elseif ($projectId) {
            $benefQuery->inProject((int) $projectId);
        } elseif ($scope === Project::PROGRAMME_EDUCATION) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_EDUCATION));
        } elseif ($scope === Project::PROGRAMME_FOOTBALL) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_FOOTBALL));
        }

        $beneficiaries = $benefQuery->orderBy('name')->get();
        $beneficiaryIds = $beneficiaries->pluck('id');

        // Calendar Structure
        $weeksStructure = $this->buildWeeksStructure($month, $year);

        // Fetch Attendance Logs
        $matrix = [];
        if ($beneficiaryIds->isNotEmpty()) {
            $attLogs = AttendanceLog::whereIn('beneficiary_id', $beneficiaryIds)
                ->whereMonth('attended_at', $month)
                ->whereYear('attended_at', $year)
                ->get();

            foreach ($attLogs as $log) {
                $d = Carbon::parse($log->attended_at)->day;
                $matrix[$log->beneficiary_id][$d] = $log->status;
            }
        }

        if ($team && !$project) {
            $project = $team->project;
        }

        $isFootball = ($project && $project->programme_type === Project::PROGRAMME_FOOTBALL)
            || ($team && $team->project?->programme_type === Project::PROGRAMME_FOOTBALL);

        if ($isFootball) {
            $coachUser = null;
            if ($team && $team->coach) {
                $coachUser = $team->coach;
            } elseif ($user && $user->isCoach()) {
                $coachUser = $user;
            } elseif ($project && ($firstTeamCoach = Team::where('project_id', $project->id)->whereNotNull('coach_id')->first()?->coach)) {
                $coachUser = $firstTeamCoach;
            }
            $instructorName = $coachUser ? (str_starts_with(strtolower($coachUser->name), 'coach') ? $coachUser->name : 'Coach ' . $coachUser->name) : 'Assigned Coach';
        } else {
            $officerUser = ($user && $user->isProjectOfficer()) ? $user : User::where('role', User::ROLE_PROJECT_OFFICER)->first();
            $instructorName = $officerUser ? $officerUser->name : ($user?->name ?? 'Class Instructor');
        }

        $carbonDate = Carbon::createFromDate($year, $month, 1);

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

    /**
     * Export the Meal Distribution Register (Cooks) PDF.
     */
    public function exportMeals(Request $request): StreamedResponse
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $user      = auth()->user();
        abort_unless($user && ($user->isHeadOfProgrammes() || $user->isSystemManager() || $user->isProjectOfficer() || $user->isCoach()), 403, 'Unauthorized export request.');

        $isCoach   = $user->isCoach();
        $isOfficer = $user->isProjectOfficer();

        $month = (int) ($request->query('month') ?: now()->month);
        $year  = (int) ($request->query('year') ?: now()->year);
        $scope = $request->query('scope', 'all');

        $projectId = $request->query('project_id');
        $teamId    = $request->query('team_id');

        // Enforce strict role-based export scoping
        if ($isCoach) {
            $coachTeam = Team::where('coach_id', $user->id)->first();
            if ($teamId && $coachTeam && (int) $teamId !== (int) $coachTeam->id) {
                abort(403, 'Unauthorized: Coaches can only export registers for their assigned team.');
            }
            $teamId    = $coachTeam ? (string) $coachTeam->id : '0';
            $projectId = $coachTeam ? (string) $coachTeam->project_id : null;
            $scope     = 'football';
        } elseif ($isOfficer && $user->assigned_project_id) {
            if ($projectId && (int) $projectId !== (int) $user->assigned_project_id) {
                abort(403, 'Unauthorized: Project Officers can only export registers for their assigned project.');
            }
            $projectId = (string) $user->assigned_project_id;
        }

        $project = $projectId ? Project::find($projectId) : Project::first();
        $team    = $teamId ? Team::find($teamId) : null;

        // Build Beneficiaries Query
        $benefQuery = Beneficiary::where('is_active', true)
            ->with(['team:id,name', 'projects:id,name,programme_type'])
            ->select(['id', 'name', 'phone_number', 'team_id', 'shortcode']);

        if ($isCoach) {
            $benefQuery->where('team_id', $teamId ?: 0);
        } elseif ($teamId) {
            $benefQuery->where('team_id', $teamId);
        } elseif ($projectId) {
            $benefQuery->inProject((int) $projectId);
        } elseif ($scope === Project::PROGRAMME_EDUCATION) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_EDUCATION));
        } elseif ($scope === Project::PROGRAMME_FOOTBALL) {
            $benefQuery->whereHas('projects', fn ($q) => $q->where('programme_type', Project::PROGRAMME_FOOTBALL));
        }

        $beneficiaries = $benefQuery->orderBy('name')->get();
        $beneficiaryIds = $beneficiaries->pluck('id');

        // Calendar Structure
        $weeksStructure = $this->buildWeeksStructure($month, $year);

        // Fetch Meal Logs
        $matrix = [];
        $totalMealsCount = 0;
        $cooksLabel = 'Kitchen Cook Terminal';

        if ($beneficiaryIds->isNotEmpty()) {
            $mealLogs = MealLog::whereIn('beneficiary_id', $beneficiaryIds)
                ->whereMonth('served_at', $month)
                ->whereYear('served_at', $year)
                ->get();

            $totalMealsCount = $mealLogs->count();

            foreach ($mealLogs as $log) {
                $d = Carbon::parse($log->served_at)->day;
                $matrix[$log->beneficiary_id][$d] = ($matrix[$log->beneficiary_id][$d] ?? 0) + 1;
            }

            $cookIds = $mealLogs->pluck('served_by_user_id')->unique()->filter();
            $cooks = User::whereIn('id', $cookIds)->pluck('name')->toArray();
            if (!empty($cooks)) {
                $cooksLabel = implode(', ', $cooks);
            }
        }

        if ($team && !$project) {
            $project = $team->project;
        }

        $carbonDate = Carbon::createFromDate($year, $month, 1);

        $pdf = Pdf::loadView('pdf.meal-distribution-register-sheet', [
            'project'         => $project,
            'team'            => $team,
            'beneficiaries'   => $beneficiaries,
            'weeksStructure'  => $weeksStructure,
            'matrix'          => $matrix,
            'month'           => $month,
            'year'            => $year,
            'monthName'       => $carbonDate->format('F Y'),
            'cooksLabel'      => $cooksLabel,
            'totalMeals'      => $totalMealsCount,
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

    private function buildWeeksStructure(int $month, int $year): array
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $weeksStructure     = [];
        $currentDate        = $startOfMonth->copy();
        $currentWeekNumber  = 1;
        $weekDaysCollection = [];

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

            if ($currentDate->dayOfWeek === Carbon::FRIDAY || $currentDate->copy()->addDay()->month !== $month) {
                if (!empty($weekDaysCollection)) {
                    $weeksStructure[$currentWeekNumber] = $weekDaysCollection;
                    $currentWeekNumber++;
                    $weekDaysCollection = [];
                }
            }

            $currentDate->addDay();
        }

        return $weeksStructure;
    }
}
