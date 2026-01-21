<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Define Primary Key if it's NOT 'id'
     * If your users table uses 'user_id' as the main key, uncomment the line below:
     */
    // protected $primaryKey = 'user_id'; 

    protected $fillable = [
        // 'user_id', // Remove this unless you are manually setting IDs
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('super_admin') || $this->can('access_admin_panel');
    }
    
    // REMOVED: public function user() { ... } 
    // A User does not belong to a User in this context; that was likely copied from the Employee model.
}