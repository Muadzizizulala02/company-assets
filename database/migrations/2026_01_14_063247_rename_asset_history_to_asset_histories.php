<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('asset_history', 'asset_histories');
    }

    public function down(): void
    {
        Schema::rename('asset_histories', 'asset_history');
    }
};