<?php

namespace App\Filament\Widgets;

use App\Models\MealLog;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TurnoutTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Feeding Trend (Last 30 Days)';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full'; // Takes full row underneath metrics

    protected function getData(): array
    {
        $dates = collect();
        $counts = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates->push($date->format('M d'));
            $counts[] = MealLog::whereDate('served_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Meals Served',
                    'data' => $counts,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill' => true,
                    'tension' => 0.2,
                ],
            ],
            'labels' => $dates->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}