<?php

namespace App\Observers;

use App\Models\AssetAssignment;
use App\Models\Asset;
use App\Models\AssetHistory;

class AssetAssignmentObserver
{
    /**
     * Handle the AssetAssignment "created" event.
     */
    public function created(AssetAssignment $assetAssignment): void
    {
        // Update asset status to 'assigned'
        $asset = Asset::find($assetAssignment->asset_id);
        if ($asset) {
            $asset->status = 'assigned';
            $asset->save();
        }

        // Create history record for assignment
        AssetHistory::create([
            'asset_id' => $assetAssignment->asset_id,
            'employee_id' => $assetAssignment->employee_id,
            'action' => 'assigned',
            'description' => "Asset assigned to {$assetAssignment->employee->first_name} {$assetAssignment->employee->last_name}",
            'action_date' => $assetAssignment->assigned_date,
        ]);
    }

    /**
     * Handle the AssetAssignment "updated" event.
     */
    public function updated(AssetAssignment $assetAssignment): void
    {
        // Check if return_date was just set (asset being returned)
        if ($assetAssignment->wasChanged('return_date') && $assetAssignment->return_date !== null) {
            // Update asset status to 'available'
            $asset = Asset::find($assetAssignment->asset_id);
            if ($asset) {
                $asset->status = 'available';
                $asset->save();
            }

            // Create history record for return
            AssetHistory::create([
                'asset_id' => $assetAssignment->asset_id,
                'employee_id' => $assetAssignment->employee_id,
                'action' => 'returned',
                'description' => "Asset returned by {$assetAssignment->employee->first_name} {$assetAssignment->employee->last_name}",
                'action_date' => $assetAssignment->return_date,
            ]);
        }
    }

    /**
     * Handle the AssetAssignment "deleted" event.
     */
    public function deleted(AssetAssignment $assetAssignment): void
    {
        // If deleting an active assignment, set asset back to available
        if ($assetAssignment->return_date === null) {
            $asset = Asset::find($assetAssignment->asset_id);
            if ($asset) {
                $asset->status = 'available';
                $asset->save();
            }

            // Create history record for deletion
            AssetHistory::create([
                'asset_id' => $assetAssignment->asset_id,
                'employee_id' => $assetAssignment->employee_id,
                'action' => 'assignment_deleted',
                'description' => "Assignment deleted. Asset returned to available status.",
                'action_date' => now(),
            ]);
        }
    }

    /**
     * Handle the AssetAssignment "restored" event.
     */
    public function restored(AssetAssignment $assetAssignment): void
    {
        //
    }

    /**
     * Handle the AssetAssignment "force deleted" event.
     */
    public function forceDeleted(AssetAssignment $assetAssignment): void
    {
        //
    }
}
