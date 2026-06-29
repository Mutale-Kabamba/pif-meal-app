<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, [
            User::ROLE_HEAD_OF_PROGRAMMES,
            User::ROLE_SYSTEM_MANAGER,
            User::ROLE_PROJECT_OFFICER,
            User::ROLE_COACH,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user  = auth()->user();
        $query = parent::getEloquentQuery()->with(['projects', 'team.project']);

        if ($user?->isProjectOfficer() && $user->assigned_project_id) {
            $query->inProject($user->assigned_project_id);
        }

        if ($user?->isCoach()) {
            $teamIds = Team::where('coach_id', $user->id)->pluck('id');
            $query->whereIn('team_id', $teamIds);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // ── Project enrolment ────────────────────────────────
                        Forms\Components\Select::make('projects')
                            ->label('Enrolled Projects')
                            ->multiple()
                            ->relationship(
                                'projects',
                                'name',
                                fn ($query) => $user?->isProjectOfficer() && $user->assigned_project_id
                                    ? $query->where('id', $user->assigned_project_id)
                                    : $query->where('is_active', true)
                            )
                            ->default(fn () => $user?->isProjectOfficer() && $user->assigned_project_id
                                ? [$user->assigned_project_id]
                                : []
                            )
                            ->required()
                            ->preload()
                            ->searchable()
                            ->helperText('Select one or both programmes this beneficiary participates in.')
                            ->columnSpanFull(),

                        // ── Team (football only) ─────────────────────────────
                        Forms\Components\Select::make('team_id')
                            ->label('Football Team')
                            ->relationship(
                                'team',
                                'name',
                                fn ($query) => $user?->isProjectOfficer() && $user->assigned_project_id
                                    ? $query->where('project_id', $user->assigned_project_id)->where('is_active', true)
                                    : $query->where('is_active', true)
                            )
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->helperText('Assign a team if enrolled in a Football project. Leave blank for Education-only beneficiaries.'),

                        // ── Identity ─────────────────────────────────────────
                        Forms\Components\TextInput::make('name')
                            ->label('Full Beneficiary Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shortcode')
                            ->label('Unique Shortcode')
                            ->maxLength(5)
                            ->disabled()
                            ->visibleOn('edit'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Enrolled Active Status')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Beneficiary Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('shortcode')
                    ->label('Shortcode')
                    ->searchable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('projects.name')
                    ->label('Projects')
                    ->badge()
                    ->separator(',')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('team.name')
                    ->label('Team')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('programme')
                    ->label('Programme')
                    ->getStateUsing(function ($record) {
                        $types = $record->projects->pluck('programme_type')->unique();
                        if ($types->contains('football') && $types->contains('education')) {
                            return 'Dual Enrolled';
                        }
                        return $types->first() === 'education' ? 'Education' : 'Football';
                    })
                    ->colors([
                        'info'    => 'Education',
                        'warning' => 'Dual Enrolled',
                        'success' => 'Football',
                    ])
                    ->sortable(false),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enrolled Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project')
                    ->label('Filter By Project')
                    ->options(fn () => Project::where('is_active', true)->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data): Builder =>
                        $data['value']
                            ? $query->inProject((int) $data['value'])
                            : $query
                    ),

                Tables\Filters\SelectFilter::make('programme_type')
                    ->label('Programme Type')
                    ->options(Project::programmeTypes())
                    ->query(fn (Builder $query, array $data): Builder =>
                        $data['value']
                            ? $query->whereHas('projects', fn ($q) => $q->where('programme_type', $data['value']))
                            : $query
                    ),

                Tables\Filters\Filter::make('dual_enrolled')
                    ->label('Dual Enrolled')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereHas('projects', fn ($q) => $q->where('programme_type', 'football'))
                        ->whereHas('projects', fn ($q) => $q->where('programme_type', 'education'))
                    ),

                Tables\Filters\SelectFilter::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'name'),

                Tables\Filters\Filter::make('is_active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Only'),

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
            'index'  => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'edit'   => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }
}
