<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Filament\Widgets\MealScheduleWidget;
use App\Filament\Widgets\AnomalyTrackerWidget;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class MealSchedulePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Meal Schedule & Auditing';
    protected static ?string $slug = 'meal-schedule';
    protected static string $view = 'filament.pages.meal-schedule-page';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'head_of_programmes';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createProject')
                ->label('New Project Stream')
                ->icon('heroicon-m-plus')
                ->color('emerald')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('daily_meal_limit_per_beneficiary')
                        ->label('Daily Meal Limit')
                        ->numeric()
                        ->required()
                        ->default(1),
                    Textarea::make('description')
                        ->label('Weekly Feeding Schedule Menu Rules')
                        ->placeholder('e.g., Monday: Nshima with Chicken...')
                        ->rows(4),
                    Toggle::make('is_active')
                        ->label('Active Operational Status')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    Project::create($data);
                    
                    Notification::make()
                        ->title('Project stream & feeding rules created.')
                        ->success()
                        ->send();

                    $this->dispatch('refresh-schedule-widgets');
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MealScheduleWidget::class,
            AnomalyTrackerWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}