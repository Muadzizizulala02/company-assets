<?php

namespace App\Policies;

use App\Models\AssetAssignment;
use App\Models\User;

class AssetAssignmentPolicy
{
    /**
     * Determine if user can view any assignments
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset::assignment');
    }

    /**
     * Determine if user can view this specific assignment
     */
    public function view(User $user, AssetAssignment $assetAssignment): bool
    {
        // Employees can only view their own assignments
        if ($user->hasRole('employee')) {
            return $assetAssignment->employee->email === $user->email;
        }

        return $user->can('view_asset::assignment');
    }

    /**
     * Determine if user can create assignments
     */
    public function create(User $user): bool
    {
        return $user->can('create_asset::assignment');
    }

    /**
     * Determine if user can update this assignment
     */
    public function update(User $user, AssetAssignment $assetAssignment): bool
    {
        return $user->can('update_asset::assignment');
    }

    /**
     * Determine if user can delete this assignment
     */
    public function delete(User $user, AssetAssignment $assetAssignment): bool
    {
        return $user->can('delete_asset::assignment');
    }
}