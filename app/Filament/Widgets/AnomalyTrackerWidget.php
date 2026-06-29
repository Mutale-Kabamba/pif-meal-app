<?php

namespace App\Filament\Widgets;

use App\Models\AnomalyLog;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AnomalyTrackerWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Critical Meal Validation Anomalies';

    public static function canView(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            User::ROLE_HEAD_OF_PROGRAMMES,
            User::ROLE_SYSTEM_MANAGER,
        ]) || request()->routeIs('filament.admin.pages.meal-schedule');
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
