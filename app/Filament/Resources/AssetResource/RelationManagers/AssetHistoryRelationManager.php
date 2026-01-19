<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AssetHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'assetHistory';

    protected static ?string $title = 'History';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'success',
                        'returned' => 'info',
                        'assignment_deleted' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->formatStateUsing(fn ($record) => $record->employee 
                        ? "{$record->employee->first_name} {$record->employee->last_name}" 
                        : 'N/A'),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('action_date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Removed create action - history is auto-generated only
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for history
            ])
            ->defaultSort('action_date', 'desc');
    }
}