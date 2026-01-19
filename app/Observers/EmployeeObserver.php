<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Check if employee doesn't already have a user account
        if (!$employee->user_id) {
            // Generate random password
            $password = Str::random(12);
            
            // Create user account
            $user = User::create([
                'name' => "{$employee->first_name} {$employee->last_name}",
                'email' => $employee->email,
                'password' => Hash::make($password),
            ]);
            
            // Assign 'employee' role
            $user->assignRole('employee');
            
            // Link employee to user
            $employee->user_id = $user->id;
            $employee->saveQuietly(); // Use saveQuietly to avoid infinite loop
            
            // TODO: Send email with password to employee
            // For now, we'll log it (in real app, send email!)
            Log::info("User created for {$employee->email} with password: {$password}");
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
        }
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        // Optionally delete the user account when employee is deleted
        if ($employee->user_id && $employee->user) {
            $employee->user->delete();
        }
    }
}