<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Check if employee doesn't already have a user account
        if (!$employee->user_id) {
            
            DB::transaction(function () use ($employee) {
                // Generate random password
                $password = Str::random(12);

                // Create user account
                $user = User::create([
                    'name' => "{$employee->first_name} {$employee->last_name}",
                    'email' => $employee->email,
                    'password' => Hash::make($password),
                ]);

                // 🛑 CRITICAL FIX: Ensure ID is loaded
                // If the DB didn't return the ID immediately, force a refresh
                if (!$user->id) {
                    $user->refresh();
                }

                // Safety Check: If ID is still null, the DB Auto-Increment is broken
                if ($user->id) {
                    // Assign 'employee' role
                    $user->assignRole('employee');

                    // Link employee to user
                    $employee->user_id = $user->id;
                    $employee->saveQuietly(); 

                    Log::info("User created for {$employee->email} with password: {$password}");
                } else {
                    // Log error so the app doesn't crash silently if ID is missing
                    Log::error("Failed to create User for Employee {$employee->id}. User ID was null. Check Database AUTO_INCREMENT.");
                }
            });
        }
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        // If employee has a user, sync the name and email
        if ($employee->user_id && $employee->user) {
            $employee->user->update([
                'name' => "{$employee->first_name} {$employee->last_name}",
                'email' => $employee->email,
            ]);
            
            // Sync roles from form
            if (request()->has('roles')) {
                $roles = request('roles');
                $employee->user->syncRoles($roles);
            }
        }
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        // Optionally delete the user account when employee is deleted
        if ($employee->user_id && $employee->user) {
            // Detach roles before deleting to avoid orphaned records (optional but clean)
            $employee->user->syncRoles([]);
            $employee->user->delete();
        }
    }
}