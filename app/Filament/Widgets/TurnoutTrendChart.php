<?php

namespace App\Filament\Widgets;

use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TurnoutTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Feeding Trend (Last 30 Days)';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $user      = auth()->user();
        $isOfficer = $user?->isProjectOfficer();
        $isCoach   = $user?->isCoach();
        $projectId = $isOfficer ? $user->assigned_project_id : null;
        $teamId    = $isCoach ? Team::where('coach_id', $user->id)->value('id') : null;

        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        $query = MealLog::selectRaw("DATE(served_at) as date_key, COUNT(*) as total")
            ->whereBetween('served_at', [$startDate, $endDate]);

        if ($isCoach && $teamId) {
            $teamBenefIds = Beneficiary::where('team_id', $teamId)->pluck('id');
            $query->whereIn('beneficiary_id', $teamBenefIds);
        } elseif ($isOfficer && $projectId) {
            $query->where('project_id', $projectId);
        }

        $logCounts = $query->groupBy('date_key')->pluck('total', 'date_key');

        $dates  = collect();
        $counts = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates->push($date->format('M d'));
            $counts[] = $logCounts->get($date->format('Y-m-d'), 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Meals Served',
                    'data'            => $counts,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill'            => true,
                    'tension'         => 0.2,
                ],
            ],
            'labels' => $dates->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
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