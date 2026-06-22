<?php

namespace App\Filament\Widgets;

use App\Models\MealLog;
use App\Models\Project;
use App\Models\Beneficiary;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class KpiCardsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $user       = auth()->user();
        $isOfficer  = $user?->isProjectOfficer();
        $projectId  = $user?->assigned_project_id;

        // Separate cache bucket per project so officers never see global totals
        $cacheKey  = $isOfficer ? "nms_kpis_project_{$projectId}" : 'nms_dashboard_kpis';

        $statsData = Cache::remember($cacheKey, 300, function () use ($isOfficer, $projectId) {
            $todayStart    = today()->startOfDay();
            $todayEnd      = today()->endOfDay();
            $weekStart     = now()->startOfWeek();
            $weekEnd       = now()->endOfWeek();
            $monthStart    = now()->startOfMonth();
            $monthEnd      = now()->endOfMonth();

            $mealsBase = MealLog::query();
            $benefBase = Beneficiary::query();

            if ($isOfficer && $projectId) {
                $mealsBase->where('project_id', $projectId);
                $benefBase->where('project_id', $projectId);
            }

            return [
                'activeProjects'     => $isOfficer ? null : Project::where('is_active', true)->count(),
                'totalBeneficiaries' => (clone $benefBase)->count(),
                'mealsToday'         => (clone $mealsBase)->whereBetween('served_at', [$todayStart, $todayEnd])->count(),
                'mealsThisWeek'      => (clone $mealsBase)->whereBetween('served_at', [$weekStart, $weekEnd])->count(),
                'mealsThisMonth'     => (clone $mealsBase)->whereBetween('served_at', [$monthStart, $monthEnd])->count(),
                'turnoutRate'        => $this->calculateTurnoutRate($isOfficer ? $projectId : null),
            ];
        });

        $stats = [];

        if (! $isOfficer) {
            $stats[] = Stat::make('Active Projects', $statsData['activeProjects'])
                ->description('Currently active streams')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success');
        }

        $stats[] = Stat::make($isOfficer ? 'My Project Beneficiaries' : 'Total Beneficiaries', $statsData['totalBeneficiaries'])
            ->description($isOfficer ? 'Enrolled in your project' : 'Registered individuals')
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
            ->description('Active population fed')
            ->descriptionIcon('heroicon-m-chart-bar-square')
            ->color('danger');

        return $stats;
    }

    private function calculateTurnoutRate(?int $projectId = null): float
    {
        $query = Beneficiary::where('is_active', true);
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        $activeBeneficiaries = $query->count();

        if ($activeBeneficiaries === 0) {
            return 0;
        }

        $todayStart = today()->startOfDay();
        $todayEnd   = today()->endOfDay();

        $fedQuery = MealLog::whereBetween('served_at', [$todayStart, $todayEnd]);
        if ($projectId) {
            $fedQuery->where('project_id', $projectId);
        }
        $fedToday = $fedQuery->distinct('beneficiary_id')->count('beneficiary_id');

        return round(($fedToday / $activeBeneficiaries) * 100, 1);
    }

    public static function canView(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            \App\Models\User::ROLE_HEAD_OF_PROGRAMMES,
            \App\Models\User::ROLE_SYSTEM_MANAGER,
            \App\Models\User::ROLE_PROJECT_OFFICER,
        ]);
    }
}