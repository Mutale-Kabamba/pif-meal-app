<?php

namespace App\Filament\Widgets;

use App\Models\MealLog;
use App\Models\Project;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class ProjectComparisonChart extends ChartWidget
{
    protected static ?string $heading = 'Meals by Project This Month';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();

        $mealCounts = MealLog::selectRaw('project_id, COUNT(*) as total')
            ->whereBetween('served_at', [$startOfMonth, $endOfMonth])
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $projects = Project::where('is_active', true)->get();
        $labels   = [];
        $data     = [];

        foreach ($projects as $project) {
            $labels[] = $project->name;
            $data[]   = $mealCounts->get($project->id, 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Meals This Month',
                    'data'            => $data,
                    'backgroundColor' => '#10b981',
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
