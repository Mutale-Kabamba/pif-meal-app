<?php

namespace App\Filament\Widgets;

use App\Models\AnomalyLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AnomalyTrackerWidget extends BaseWidget
{
    protected static ?int $sort = 4; // Positioned last beneath the analytics charts row
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Critical Meal Validation Anomalies';

    public static function canView(): bool
    {
        // View restricted to Head of Programmes or if required on specific dashboard pages
        $role = auth()->user()?->role;
        return $role === 'head_of_programmes' || request()->routeIs('filament.admin.pages.meal-schedule');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(AnomalyLog::query()->latest('attempted_at'))
            ->columns([
                Tables\Columns\TextColumn::make('attempted_at')
                    ->label('Timestamp')
                    ->dateTime('M d, h:i A'),
                Tables\Columns\TextColumn::make('beneficiary.name')
                    ->label('Beneficiary'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project Sector'),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Violation Reason')
                    ->color('danger')
                    ->wrap(),
            ]);
    }
}