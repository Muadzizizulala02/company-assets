<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('assets_assignments', 'asset_assignments');
    }

    public function down(): void
    {
        Schema::rename('asset_assignments', 'assets_assignments');
    }
};