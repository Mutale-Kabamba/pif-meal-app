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
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Single grouped query instead of one query per project
        $mealCounts = MealLog::selectRaw('project_id, COUNT(*) as total')
            ->whereBetween('served_at', [$startOfMonth, $endOfMonth])
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $projects = Project::where('is_active', true)->get();
        $labels = [];
        $data = [];

        foreach ($projects as $project) {
            $labels[] = $project->name;
            $data[] = $mealCounts->get($project->id, 0);
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