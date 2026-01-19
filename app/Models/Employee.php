<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Employee extends Model
{
    use HasFactory;

    // Lists which fields can be filled using create() or update() methods
    // Protects against hackers injecting unwanted data
    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'department',
        'position',
        'hire_date',
        'status',
    ];

    // cast macam dia bagi convert data type
    // for example date string to date object
    protected $casts = [
        'hire_date' => 'date',
        'status' => 'string',
    ];


    // Relationship: One employee can have many asset assignments
    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    // Relationship: One employee can have many history records
    public function assetHistory(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    // Helper method: Get currently assigned assets
    // can use at controller, blade, api or tinker
    // so x buat bnyk kali code
    public function currentAssets()
    {
        return $this->assetAssignments()
            ->whereNull('return_date')
            ->with('asset')
            ->get();
    }

    // Relationship: Employee belongs to one user account
    public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
}
