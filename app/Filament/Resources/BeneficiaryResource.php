<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Models\Beneficiary;
use App\Models\Project;
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
        // Permits both Head of Programmes and System Managers to access management controls
        $role = auth()->user()?->role;
        return $role === 'head_of_programmes' || $role === 'system_manager';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('Assigned Nutritional Stream Project')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Full Beneficiary Name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('shortcode')
                            ->label('Unique Shortcode Identification')
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
                    ->label('Shortcode Token')
                    ->searchable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color('primary')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project Stream')
                    ->sortable()
                    ->wrap(),
                
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
                    ->relationship('project', 'name')
                    ->label('Filter By Project Stream'),
                
                Tables\Filters\Filter::make('is_active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Beneficiaries Only'),
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
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'edit' => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }
}