<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AssetAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'assigned_date',
        'return_date',
        'notes',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'return_date' => 'date',
    ];

    // Relationship: Assignment belongs to one asset
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Relationship: Assignment belongs to one employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper method: Check if asset is currently assigned (not returned)
    public function isActive(): bool
    {
        return $this->return_date === null;
    }

    // Helper method: Get assignment duration
    public function duration()
    {
        $end = $this->return_date ?? now();
        return $this->assigned_date->diffInDays($end);
    }

    // ✨ NEW: Validation before saving
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            // Check if asset is already assigned to someone else
            $existingAssignment = self::where('asset_id', $assignment->asset_id)
                ->whereNull('return_date')
                ->first();

            if ($existingAssignment) {
                throw new \Exception("This asset is already assigned to {$existingAssignment->employee->first_name} {$existingAssignment->employee->last_name}. Please return it first.");
            }

        });
    }
}