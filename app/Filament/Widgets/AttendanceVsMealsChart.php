<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceLog;
use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AttendanceVsMealsChart extends ChartWidget
{
    protected static ?string $heading = 'Attendance vs Meals Served (Last 7 Days)';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected static ?string $pollingInterval = '15s';

    protected function getData(): array
    {
        $user      = auth()->user();
        $isOfficer = $user?->isProjectOfficer();
        $isCoach   = $user?->isCoach();
        $projectId = $isOfficer ? $user->assigned_project_id : null;
        $teamId    = $isCoach ? Team::where('coach_id', $user->id)->value('id') : null;

        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $endDate   = Carbon::today()->endOfDay();

        $attQuery = AttendanceLog::selectRaw("DATE(attended_at) as date_key, COUNT(*) as total")
            ->whereBetween('attended_at', [$startDate, $endDate])
            ->whereIn('status', [AttendanceLog::STATUS_PRESENT, AttendanceLog::STATUS_LATE]);

        $mealQuery = MealLog::selectRaw("DATE(served_at) as date_key, COUNT(*) as total")
            ->whereBetween('served_at', [$startDate, $endDate]);

        if ($isCoach && $teamId) {
            $teamBenefIds = Beneficiary::where('team_id', $teamId)->pluck('id');
            $attQuery->whereIn('beneficiary_id', $teamBenefIds);
            $mealQuery->whereIn('beneficiary_id', $teamBenefIds);
        } elseif ($isOfficer && $projectId) {
            $projectBenefIds = Beneficiary::inProject($projectId)->pluck('id');
            $attQuery->whereIn('beneficiary_id', $projectBenefIds);
            $mealQuery->whereIn('beneficiary_id', $projectBenefIds);
        }

        $attCounts  = $attQuery->groupBy('date_key')->pluck('total', 'date_key');
        $mealCounts = $mealQuery->groupBy('date_key')->pluck('total', 'date_key');

        $labels   = [];
        $attData  = [];
        $mealData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $key  = $date->format('Y-m-d');

            $labels[]   = $date->format('D, M j');
            $attData[]  = (int) ($attCounts->get($key, 0));
            $mealData[] = (int) ($mealCounts->get($key, 0));
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Session Attendees',
                    'data'            => $attData,
                    'backgroundColor' => '#059669',
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Meals Served',
                    'data'            => $mealData,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            User::ROLE_HEAD_OF_PROGRAMMES,
            User::ROLE_SYSTEM_MANAGER,
            User::ROLE_PROJECT_OFFICER,
            User::ROLE_COACH,
        ]);
    }
}
