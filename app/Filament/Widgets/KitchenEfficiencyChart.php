<?php

namespace App\Filament\Widgets;

use App\Models\MealLog;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class KitchenEfficiencyChart extends ChartWidget
{
    protected static ?string $heading = 'Meals Served by Cook (Today)';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $todayStart = today()->startOfDay();
        $todayEnd   = today()->endOfDay();

        $mealCounts = MealLog::selectRaw('served_by_user_id, COUNT(*) as total')
            ->whereBetween('served_at', [$todayStart, $todayEnd])
            ->groupBy('served_by_user_id')
            ->pluck('total', 'served_by_user_id');

        $cooks  = User::where('role', User::ROLE_COOK)->get();
        $labels = [];
        $data   = [];

        foreach ($cooks as $cook) {
            $labels[] = $cook->name;
            $data[]   = $mealCounts->get($cook->id, 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Meals Today',
                    'data'            => $data,
                    'backgroundColor' => '#3b82f6',
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
        ]);
    }
}
