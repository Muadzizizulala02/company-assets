<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'action',
        'description',
        'action_date',
    ];

    protected $casts = [
        'action_date' => 'datetime',
    ];

    // Relationship: History record belongs to one asset
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Relationship: History record belongs to one employee (nullable)
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Automatically set action_date when creating record
    // If action_date is not set, automatically set it to current time
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($history) {
            if (!$history->action_date) {
                $history->action_date = now();
            }
        });
    }
}