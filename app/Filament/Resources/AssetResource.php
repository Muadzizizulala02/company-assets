<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\AssetResource\RelationManagers;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asset Details')
                    ->schema([
                        Forms\Components\TextInput::make('asset_tag')
                            ->label('Asset Tag')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('LAP-001, MON-042'),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Dell XPS 15'),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'Laptop' => 'Laptop',
                                'Desktop' => 'Desktop',
                                'Monitor' => 'Monitor',
                                'Phone' => 'Phone',
                                'Tablet' => 'Tablet',
                                'Keyboard' => 'Keyboard',
                                'Mouse' => 'Mouse',
                                'Printer' => 'Printer',
                                'Other' => 'Other',
                            ])
                            ->required()
                            ->searchable(),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'assigned' => 'Assigned',
                                'under_repair' => 'Under Repair',
                                'retired' => 'Retired',
                            ])
                            ->required()
                            ->default('available'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Specifications')
                    ->schema([
                        Forms\Components\TextInput::make('brand')
                            ->maxLength(255)
                            ->placeholder('Dell, Apple, HP'),
                        
                        Forms\Components\TextInput::make('model')
                            ->maxLength(255)
                            ->placeholder('XPS 15 9520'),
                        
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('SN123456789'),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->placeholder('16GB RAM, 512GB SSD, etc.'),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Purchase Information')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->numeric()
                            ->prefix('RM')
                            ->maxValue(999999.99)
                            ->placeholder('1500.00'),
                        
                        Forms\Components\DatePicker::make('purchase_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\TextInput::make('supplier')
                            ->maxLength(255)
                            ->placeholder('Amazon, Dell Official'),
                        
                        Forms\Components\TextInput::make('warranty_months')
                            ->label('Warranty (Months)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(120)
                            ->suffix('months')
                            ->placeholder('24'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_tag')
                    ->label('Tag')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('brand')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'assigned' => 'warning',
                        'under_repair' => 'danger',
                        'retired' => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('purchase_price')
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Laptop' => 'Laptop',
                        'Desktop' => 'Desktop',
                        'Monitor' => 'Monitor',
                        'Phone' => 'Phone',
                        'Tablet' => 'Tablet',
                        'Keyboard' => 'Keyboard',
                        'Mouse' => 'Mouse',
                        'Printer' => 'Printer',
                        'Other' => 'Other',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'assigned' => 'Assigned',
                        'under_repair' => 'Under Repair',
                        'retired' => 'Retired',
                    ])
                    ->multiple(),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\AssetHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}