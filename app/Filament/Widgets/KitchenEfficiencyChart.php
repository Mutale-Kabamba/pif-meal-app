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
        $todayEnd = today()->endOfDay();

        // Single grouped query instead of one query per cook
        $mealCounts = MealLog::selectRaw('served_by_user_id, COUNT(*) as total')
            ->whereBetween('served_at', [$todayStart, $todayEnd])
            ->groupBy('served_by_user_id')
            ->pluck('total', 'served_by_user_id');

        $cooks = User::where('role', 'cook')->get();
        $labels = [];
        $data = [];

        foreach ($cooks as $cook) {
            $labels[] = $cook->name;
            $data[] = $mealCounts->get($cook->id, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Meals Today',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6', // Clean Blue Bar fill
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
        return auth()->user()?->role === 'head_of_programmes';
    }
}