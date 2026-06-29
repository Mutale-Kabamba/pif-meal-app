<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename the programme_type value from 'literacy' to 'education' for existing rows
        DB::table('projects')
            ->where('programme_type', 'literacy')
            ->update(['programme_type' => 'education']);

        // MySQL: update the ENUM definition
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN programme_type ENUM('football','education') NOT NULL DEFAULT 'football'");
        }
    }

    public function down(): void
    {
        DB::table('projects')
            ->where('programme_type', 'education')
            ->update(['programme_type' => 'literacy']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN programme_type ENUM('football','literacy') NOT NULL DEFAULT 'football'");
        }
    }
};
