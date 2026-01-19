<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Observers\AssetAssignmentObserver;
use App\Models\AssetAssignment;
use App\Observers\EmployeeObserver;
use App\Models\Employee;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the AssetAssignmentObserver to observe AssetAssignment model events
        AssetAssignment::observe(AssetAssignmentObserver::class);
        // Register the EmployeeObserver to observe Employee model events
        Employee::observe(EmployeeObserver::class); // ✨ ADD THIS

    }
}
