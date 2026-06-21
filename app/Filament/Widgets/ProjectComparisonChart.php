<?php

namespace App\Filament\Widgets;

use App\Models\MealLog;
use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectComparisonChart extends ChartWidget
{
    protected static ?string $heading = 'Meals by Project';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $projects = Project::where('is_active', true)->get();
        $labels = [];
        $data = [];

        foreach ($projects as $project) {
            $labels[] = $project->name;
            $data[] = MealLog::where('project_id', $project->id)
                ->whereMonth('served_at', now()->month)
                ->whereYear('served_at', now()->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Meals This Month',
                    'data' => $data,
                    'backgroundColor' => '#10b981', // Clean Emerald Green Bar fill
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Changed from doughnut to bar chart layout
    }

    public static function canView(): bool
    {
        return auth()->user()?->role === 'head_of_programmes';
    }
}