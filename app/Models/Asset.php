<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;

// saja nak guna kt tinker nak amik path ja
use App\Models\Employee;


class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'name',
        'category',
        'brand',
        'model',
        'serial_number',
        'description',
        'purchase_price',
        'purchase_date',
        'supplier',
        'warranty_months',
        'status',
        'image_path',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'warranty_months' => 'integer',
    ];

    // Relationship: One asset can have many assignments
    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    // Relationship: One asset can have many history records
    public function assetHistory(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    // Helper method: Get current assignment (if any)
    public function currentAssignment()
    {
        return $this->assetAssignments()
            ->whereNull('return_date')
            ->with('employee')
            ->first();  // Only one current assignment possible
    }

    // Helper method: Check if asset is currently assigned
    public function isAssigned(): bool
    {
        return $this->currentAssignment() !== null;
    }

    // Helper method: Get warranty expiry date
    public function warrantyExpiryDate()
    {
        if ($this->purchase_date && $this->warranty_months) {
            return $this->purchase_date->addMonths($this->warranty_months);
        }
        return null;
    }
}
