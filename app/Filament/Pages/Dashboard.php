<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\KpiCardsWidget;
use App\Filament\Widgets\TurnoutTrendChart;
use App\Filament\Widgets\ProjectComparisonChart;
use App\Filament\Widgets\KitchenEfficiencyChart;
use App\Filament\Widgets\AnomalyTrackerWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * Define the exact widgets that belong on the main dashboard homepage.
     * This bypasses auto-discovery for this page completely.
     */
    public function getWidgets(): array
    {
        return [
            KpiCardsWidget::class,
            TurnoutTrendChart::class,
            ProjectComparisonChart::class,
            KitchenEfficiencyChart::class,
            AnomalyTrackerWidget::class, // Sitting safely at the very bottom
        ];
    }

    /**
     * Set the number of grid columns for the dashboard layout.
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }
}