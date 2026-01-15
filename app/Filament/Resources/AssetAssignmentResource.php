<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetAssignmentResource\Pages;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetAssignmentResource extends Resource
{
    protected static ?string $model = AssetAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Asset Management';

    protected static ?string $navigationLabel = 'Assignments';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Assignment Details')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name} ({$record->employee_id})")
                            ->searchable(['first_name', 'last_name', 'employee_id'])
                            ->required()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('employee_id')
                                    ->required(),
                                Forms\Components\TextInput::make('first_name')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('asset_id')
                            ->label('Asset')
                            ->relationship('asset', 'name', function ($query) {
                                // Only show available assets (not currently assigned)
                                return $query->where('status', 'available');
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->asset_tag})")
                            ->searchable(['name', 'asset_tag'])
                            ->required()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('asset_tag')
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\Select::make('category')
                                    ->options([
                                        'Laptop' => 'Laptop',
                                        'Monitor' => 'Monitor',
                                        'Phone' => 'Phone',
                                    ])
                                    ->required(),
                            ]),

                        Forms\Components\DatePicker::make('assigned_date')
                            ->label('Assigned Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),

                        Forms\Components\DatePicker::make('return_date')
                            ->label('Return Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(fn ($get) => $get('assigned_date'))

                            ->helperText('Leave empty if asset is still with employee'),

                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->placeholder('Reason for assignment, project details, etc.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->formatStateUsing(fn($record) => "{$record->employee->first_name} {$record->employee->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Asset')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('asset.asset_tag')
                    ->label('Asset Tag')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('asset.category')
                    ->label('Category')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('assigned_date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('return_date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Still assigned')
                    ->badge()
                    ->color(fn($state) => $state === null ? 'success' : 'gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->return_date === null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('active_only')
                    ->label('Active Assignments Only')
                    ->query(fn($query) => $query->whereNull('return_date'))
                    ->toggle(),

                Tables\Filters\Filter::make('returned_only')
                    ->label('Returned Assignments Only')
                    ->query(fn($query) => $query->whereNotNull('return_date'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('assigned_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssetAssignments::route('/'),
            'create' => Pages\CreateAssetAssignment::route('/create'),
            'edit' => Pages\EditAssetAssignment::route('/{record}/edit'),
        ];
    }
}
