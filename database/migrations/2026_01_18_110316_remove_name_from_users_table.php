<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if the column exists
        if (Schema::hasColumn('users', 'name')) {
            // Option 1: Simply remove the column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });

            // Option 2: If you want to backup data first (recommended for production)
            // 1. Create a backup table or column
            // 2. Remove the column
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the column back with the same properties
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        // If you need to restore data from backup, do it here
    }
};
