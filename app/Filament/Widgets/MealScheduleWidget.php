<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class MealScheduleWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Program Menu Configurations & Ration Rules';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'head_of_programmes';
    }

    #[On('refresh-schedule-widgets')]
    public function refresh()
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Project Stream Name')
                    ->weight('bold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('budget_code')
                    ->label('Budget Code')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('daily_meal_limit_per_beneficiary')
                    ->label(' Rations/Day Limit')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Active Weekly Menu Schedule Rules')
                    ->wrap()
                    ->placeholder('No specific weekly feeding menu configured yet.'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Operational Status')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        TextInput::make('name')->required(),
                        TextInput::make('budget_code')->required(),
                        TextInput::make('daily_meal_limit_per_beneficiary')->numeric()->required(),
                        Textarea::make('description')->label('Weekly Feeding Schedule Menu Rules')->rows(4),
                        Toggle::make('is_active'),
                    ])
                    ->successNotificationTitle('Feeding schedule rules updated configuration.'),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Project feeding configuration permanently purged.'),
            ]);
    }
}