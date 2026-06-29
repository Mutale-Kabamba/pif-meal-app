<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            User::ROLE_HEAD_OF_PROGRAMMES,
            User::ROLE_SYSTEM_MANAGER,
            User::ROLE_PROJECT_OFFICER,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user  = auth()->user();
        $query = parent::getEloquentQuery();

        // Project officers only see teams in their own project
        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $query->where('project_id', $user->assigned_project_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Team Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. U12 Lions'),

                Forms\Components\Select::make('project_id')
                    ->label('Football Project')
                    ->options(
                        fn () => Project::where('is_active', true)
                            ->where('programme_type', Project::PROGRAMME_FOOTBALL)
                            ->pluck('name', 'id')
                    )
                    ->helperText('Teams can only belong to Football projects.')
                    ->required()
                    ->searchable()
                    ->hidden(fn () => auth()->user()?->isProjectOfficer()),

                Forms\Components\Placeholder::make('project_locked')
                    ->label('Football Project')
                    ->content(fn () => auth()->user()?->assignedProject?->name ?? '—')
                    ->visible(fn () => auth()->user()?->isProjectOfficer()),

                Forms\Components\Select::make('coach_id')
                    ->label('Coach')
                    ->options(
                        fn () => User::where('role', User::ROLE_COACH)
                            ->orWhere('role', User::ROLE_PROJECT_OFFICER)
                            ->pluck('name', 'id')
                    )
                    ->nullable()
                    ->searchable()
                    ->placeholder('Unassigned'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Team Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Football Project')
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('coach.name')
                    ->label('Coach')
                    ->placeholder('Unassigned')
                    ->sortable(),

                Tables\Columns\TextColumn::make('beneficiaries_count')
                    ->label('Players')
                    ->counts('beneficiaries')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->label('Filter By Project'),

                Tables\Filters\Filter::make('is_active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Teams Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit'   => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
