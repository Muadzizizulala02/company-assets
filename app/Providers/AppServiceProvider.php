<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Observers\AssetAssignmentObserver;
use App\Models\AssetAssignment;



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
    }
}
