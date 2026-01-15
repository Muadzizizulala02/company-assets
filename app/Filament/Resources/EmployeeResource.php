<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'HR Management'; // - **Groups related Resources together in sidebar**

    protected static ?int $navigationSort = 1; // Controls order in sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Information') // - **Groups related fields together**
                    ->schema([
                        Forms\Components\TextInput::make('employee_id')
                            ->label('Employee ID')
                            ->required()
                            // Without ignoreRecord: ❌ "Error: employee_id already exists" (it's checking against itself!)
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            // ->placeholder('EMP-001'),
                        
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])
                    ->columns(2), // berapa banyak column dlm section in horizontal direction

                Forms\Components\Section::make('Contact & Position') // // - **Groups related fields together**
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('department')
                            ->maxLength(255)
                            ->placeholder('IT, HR, Marketing, etc.'),
                        
                        Forms\Components\TextInput::make('position')
                            ->maxLength(255)
                            ->placeholder('Developer, Manager, etc.'),
                        
                        Forms\Components\DatePicker::make('hire_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2), // berapa banyak column dlm section in horizontal direction
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                
                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->badge()
                    ->color('info'), // displays as colored pill:
                
                Tables\Columns\TextColumn::make('position')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('hire_date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                
                Tables\Filters\SelectFilter::make('department')
                    ->options([
                        'IT' => 'IT',
                        'HR' => 'HR',
                        'Marketing' => 'Marketing',
                        'Finance' => 'Finance',
                        'Operations' => 'Operations',
                    ]),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}