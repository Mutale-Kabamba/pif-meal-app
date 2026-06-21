<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProjectRegistersWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Global Project Operational Summary Registers';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'head_of_programmes';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->withCount(['beneficiaries', 'mealLogs']))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Project Name')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('budget_code')
                    ->label('Budget Code')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('beneficiaries_count')
                    ->label('Active Beneficiaries')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('meal_logs_count')
                    ->label('Meals Distributed')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
            ]);
    }
}