<?php

namespace App\Filament\Widgets;

use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class KpiCardsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $user      = auth()->user();
        $isOfficer = $user?->isProjectOfficer();
        $isCoach   = $user?->isCoach();
        $projectId = $user?->assigned_project_id;
        $teamId    = $isCoach ? Team::where('coach_id', $user->id)->value('id') : null;

        if ($isCoach) {
            $cacheKey = "nms_kpis_team_{$teamId}";
        } elseif ($isOfficer) {
            $cacheKey = "nms_kpis_project_{$projectId}";
        } else {
            $cacheKey = 'nms_dashboard_kpis';
        }

        $statsData = Cache::remember($cacheKey, 300, function () use ($isOfficer, $isCoach, $projectId, $teamId) {
            $todayStart = today()->startOfDay();
            $todayEnd   = today()->endOfDay();
            $weekStart  = now()->startOfWeek();
            $weekEnd    = now()->endOfWeek();
            $monthStart = now()->startOfMonth();
            $monthEnd   = now()->endOfMonth();

            $mealsBase = MealLog::query();
            $benefBase = Beneficiary::query();

            if ($isCoach && $teamId) {
                // Scope to the coach's own team
                $benefBase->where('team_id', $teamId);
                $teamBenefIds = Beneficiary::where('team_id', $teamId)->pluck('id');
                $mealsBase->whereIn('beneficiary_id', $teamBenefIds);
            } elseif ($isOfficer && $projectId) {
                $benefBase->inProject($projectId);
                $mealsBase->where('project_id', $projectId);
            }

            return [
                'activeProjects'     => ($isOfficer || $isCoach) ? null : Project::where('is_active', true)->count(),
                'totalBeneficiaries' => (clone $benefBase)->count(),
                'mealsToday'         => (clone $mealsBase)->whereBetween('served_at', [$todayStart, $todayEnd])->count(),
                'mealsThisWeek'      => (clone $mealsBase)->whereBetween('served_at', [$weekStart, $weekEnd])->count(),
                'mealsThisMonth'     => (clone $mealsBase)->whereBetween('served_at', [$monthStart, $monthEnd])->count(),
                'turnoutRate'        => $this->calculateTurnoutRate($isOfficer ? $projectId : null, $isCoach ? $teamId : null),
            ];
        });

        $stats = [];

        if (!$isOfficer && !$isCoach) {
            $stats[] = Stat::make('Active Projects', $statsData['activeProjects'])
                ->description('Currently active streams')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success');
        }

        $benefLabel = match (true) {
            $isCoach   => 'My Team Beneficiaries',
            $isOfficer => 'My Project Beneficiaries',
            default    => 'Total Beneficiaries',
        };
        $benefDesc = match (true) {
            $isCoach   => 'Enrolled in your team',
            $isOfficer => 'Enrolled in your project',
            default    => 'Registered individuals',
        };

        $stats[] = Stat::make($benefLabel, $statsData['totalBeneficiaries'])
            ->description($benefDesc)
            ->descriptionIcon('heroicon-m-users')
            ->color('info');

        $stats[] = Stat::make('Meals Today', $statsData['mealsToday'])
            ->description('Distributed today')
            ->descriptionIcon('heroicon-m-cake')
            ->color('warning');

        $stats[] = Stat::make('This Week', $statsData['mealsThisWeek'])
            ->description('Current week total')
            ->descriptionIcon('heroicon-m-calendar-days')
            ->color('primary');

        $stats[] = Stat::make('This Month', $statsData['mealsThisMonth'])
            ->description('Current month total')
            ->descriptionIcon('heroicon-m-clock')
            ->color('indigo');

        $stats[] = Stat::make('Turnout Rate', $statsData['turnoutRate'] . '%')
            ->description('Active population fed today')
            ->descriptionIcon('heroicon-m-chart-bar-square')
            ->color('danger');

        return $stats;
    }

    private function calculateTurnoutRate(?int $projectId = null, ?int $teamId = null): float
    {
        $query = Beneficiary::where('is_active', true);

        if ($teamId) {
            $query->where('team_id', $teamId);
        } elseif ($projectId) {
            $query->inProject($projectId);
        }

        $activeBeneficiaries = $query->count();

        if ($activeBeneficiaries === 0) {
            return 0;
        }

        $todayStart = today()->startOfDay();
        $todayEnd   = today()->endOfDay();

        $fedQuery = MealLog::whereBetween('served_at', [$todayStart, $todayEnd]);

        if ($teamId) {
            $teamBenefIds = Beneficiary::where('team_id', $teamId)->pluck('id');
            $fedQuery->whereIn('beneficiary_id', $teamBenefIds);
        } elseif ($projectId) {
            $fedQuery->where('project_id', $projectId);
        }

        $fedToday = $fedQuery->distinct('beneficiary_id')->count('beneficiary_id');

        return round(($fedToday / $activeBeneficiaries) * 100, 1);
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