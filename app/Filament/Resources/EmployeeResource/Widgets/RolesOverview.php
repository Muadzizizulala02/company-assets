<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class RolesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];
        
        $roles = Role::all();
        
        foreach ($roles as $role) {
            $userCount = User::role($role->name)->count();
            
            $stats[] = Stat::make($role->name, $userCount)
                ->description('users with this role')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($this->getRoleColor($role->name));
        }
        
        return $stats;
    }
    
    protected function getRoleColor(string $roleName): string
    {
        return match ($roleName) {
            'super_admin' => 'danger',
            'admin' => 'warning',
            'hr_manager' => 'info',
            'it_manager' => 'success',
            'employee' => 'gray',
            default => 'primary',
        };
    }
}