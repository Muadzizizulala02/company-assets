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
use Illuminate\Support\Facades\Auth; // ✨ Imported for permission checks

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
                            ->unique(ignoreRecord: true) // Without ignoreRecord: ❌ "Error: employee_id already exists"
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

                // ✨ ADD THIS NEW SECTION
                Forms\Components\Section::make('User Account & Permissions')
                    ->schema([
                        Forms\Components\Placeholder::make('user_account_status')
                            ->label('User Account Status')
                            ->content(fn($record) => $record && $record->user_id
                                ? '✅ User account exists'
                                : '⚠️ No user account (will be created automatically)')
                            ->hidden(fn($context) => $context === 'create'),

                        Forms\Components\Select::make('roles')
                            ->label('User Roles')
                            // ❌ REMOVED: ->relationship() 
                            // Reason: This forces Filament to save immediately, causing the "model_id null" crash.

                            ->options(function () {
                                return \Spatie\Permission\Models\Role::pluck('name', 'name');
                            })
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Select roles for this employee\'s user account. Multiple roles can be assigned.')
                            ->default(['employee'])
                            
                            // 🔒 SECURITY: Only Super Admin can change roles in the form
                            // If disabled, the 'roles' data is not sent, so Observer defaults to 'employee'
                            ->disabled(fn () => ! Auth::user()->hasRole('super_admin'))

                            // ✅ CRITICAL: Stop Filament from trying to save this field directly.
                            // Your Observer will handle saving the roles via request('roles').
                            ->dehydrated(false)

                            // ✅ NEW: Manually load existing roles when opening the Edit page.
                            // Since we removed ->relationship(), we need this to show current roles.
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record && $record->user) {
                                    // Load the user's current roles into the select field
                                    $component->state($record->user->roles->pluck('name')->toArray());
                                }
                            }),

                        Forms\Components\Placeholder::make('roles_note')
                            ->label('Note')
                            ->content('User account will be created automatically with "employee" role. You can change roles after creation.')
                            ->hidden(fn($context) => $context === 'edit'),
                    ])
                    ->collapsed()
                    ->visible(fn($record) => $record === null || $record->user_id),

                Forms\Components\Section::make('Contact & Position') // - **Groups related fields together**
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

                // ✨ ADD THIS
                Tables\Columns\TextColumn::make('user.roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->color('success')
                    ->default('No user account'),

                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->badge()
                    ->color('info'), // displays as colored pill:

                Tables\Columns\TextColumn::make('position')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
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
                
                // ✨ ADD THIS: Quick Role Management
                Tables\Actions\Action::make('manage_roles')
                    ->label('Manage Roles')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    // 🔒 SECURITY: Only visible if user exists AND current user is Super Admin
                    ->visible(fn($record) => $record->user_id !== null && Auth::user()->hasRole('super_admin'))
                    ->form([
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Assign Roles')
                            ->options(function () {
                                return \Spatie\Permission\Models\Role::pluck('name', 'name');
                            })
                            ->default(fn($record) => $record->user->roles->pluck('name')->toArray())
                            ->columns(2)
                            ->required()
                            ->helperText('Select all roles that apply to this employee.'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->user->syncRoles($data['roles']);

                        \Filament\Notifications\Notification::make()
                            ->title('Roles Updated')
                            ->success()
                            ->body("Roles updated for {$record->first_name} {$record->last_name}")
                            ->send();
                    }),
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