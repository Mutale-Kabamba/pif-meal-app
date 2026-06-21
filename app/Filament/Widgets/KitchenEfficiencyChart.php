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
        $cooks = User::where('role', 'cook')->get();
        $labels = [];
        $data = [];

        foreach ($cooks as $cook) {
            $labels[] = $cook->name;
            $data[] = MealLog::where('served_by_user_id', $cook->id)
                ->whereDate('served_at', today())
                ->count();
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