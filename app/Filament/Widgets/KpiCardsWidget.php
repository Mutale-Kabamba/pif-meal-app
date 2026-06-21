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
        // Cache data for 5 minutes to allow fast tab navigation across the system
        $statsData = Cache::remember('nms_dashboard_kpis', 300, function () {
            return [
                'activeProjects' => Project::where('is_active', true)->count(),
                'totalBeneficiaries' => Beneficiary::count(),
                'mealsToday' => MealLog::whereDate('served_at', today())->count(),
                'mealsThisWeek' => MealLog::whereBetween('served_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'mealsThisMonth' => MealLog::whereMonth('served_at', now()->month)->whereYear('served_at', now()->year)->count(),
                'turnoutRate' => $this->calculateTurnoutRate(),
            ];
        });

        return [
            Stat::make('Active Projects', $statsData['activeProjects'])
                ->description('Currently active streams')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success'),

            Stat::make('Total Beneficiaries', $statsData['totalBeneficiaries'])
                ->description('Registered individuals')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Meals Today', $statsData['mealsToday'])
                ->description('Distributed today')
                ->descriptionIcon('heroicon-m-cake')
                ->color('warning'),

            Stat::make('This Week', $statsData['mealsThisWeek'])
                ->description('Current week total')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('This Month', $statsData['mealsThisMonth'])
                ->description('Current month total')
                ->descriptionIcon('heroicon-m-clock')
                ->color('indigo'),

            Stat::make('Turnout Rate', $statsData['turnoutRate'] . '%')
                ->description('Active population fed')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('danger'),
        ];
    }

    private function calculateTurnoutRate(): float
    {
        $activeBeneficiaries = Beneficiary::where('is_active', true)->count();
        if ($activeBeneficiaries === 0) {
            return 0;
        }

        $fedToday = MealLog::whereDate('served_at', today())->distinct('beneficiary_id')->count();
        return round(($fedToday / $activeBeneficiaries) * 100, 1);
    }

    public static function canView(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            \App\Models\User::ROLE_HEAD_OF_PROGRAMMES,
            \App\Models\User::ROLE_SYSTEM_MANAGER,
        ]);
    }
}