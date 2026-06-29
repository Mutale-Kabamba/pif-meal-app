<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'System Management';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->isHeadOfProgrammes() || $user?->isSystemManager();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->required()
                    ->live()
                    ->options(function () {
                        $current = auth()->user();
                        $all = [
                            User::ROLE_HEAD_OF_PROGRAMMES => 'Head of Programmes',
                            User::ROLE_SYSTEM_MANAGER     => 'System Manager',
                            User::ROLE_PROJECT_OFFICER    => 'Project Officer',
                            User::ROLE_COACH              => 'Coach',
                            User::ROLE_COOK               => 'Cook',
                        ];
                        // System managers cannot create HoP or project officer accounts
                        if ($current?->isSystemManager()) {
                            unset($all[User::ROLE_HEAD_OF_PROGRAMMES]);
                            unset($all[User::ROLE_PROJECT_OFFICER]);
                        }
                        return $all;
                    }),
                Forms\Components\Select::make('assigned_project_id')
                    ->label('Assigned Project Stream')
                    ->options(fn () => Project::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->required(fn (callable $get) => $get('role') === User::ROLE_PROJECT_OFFICER)
                    ->visible(fn (callable $get) => in_array($get('role'), [
                        User::ROLE_PROJECT_OFFICER,
                        User::ROLE_COOK,
                    ])),

                Forms\Components\Select::make('coached_team_id')
                    ->label('Coached Team')
                    ->helperText('This coach will be assigned to the selected football team.')
                    ->options(
                        fn () => Team::with('project')
                            ->whereHas('project', fn ($q) => $q->where('programme_type', Project::PROGRAMME_FOOTBALL))
                            ->get()
                            ->mapWithKeys(fn (Team $team) => [
                                $team->id => $team->name . ' — ' . ($team->project->name ?? '?'),
                            ])
                    )
                    ->searchable()
                    ->nullable()
                    ->placeholder('No team assigned')
                    ->dehydrated(false)
                    ->visible(fn (callable $get) => $get('role') === User::ROLE_COACH),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        User::ROLE_HEAD_OF_PROGRAMMES => 'Head of Programmes',
                        User::ROLE_SYSTEM_MANAGER     => 'System Manager',
                        User::ROLE_PROJECT_OFFICER    => 'Project Officer',
                        User::ROLE_COACH              => 'Coach',
                        User::ROLE_COOK               => 'Cook',
                        default                       => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        User::ROLE_HEAD_OF_PROGRAMMES => 'danger',
                        User::ROLE_SYSTEM_MANAGER     => 'warning',
                        User::ROLE_PROJECT_OFFICER    => 'success',
                        User::ROLE_COACH              => 'info',
                        default                       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignedProject.name')
                    ->label('Assigned Project')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}