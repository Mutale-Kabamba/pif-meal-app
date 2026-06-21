<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Indexes for Meal Log performance optimizations
        Schema::table('meal_logs', function (Blueprint $table) {
            $table->index('served_at');
            $table->index(['project_id', 'served_at']); // Composite index for the Monthly Register matrix
        });

        // Indexes for Anomaly Tracking performance optimizations
        Schema::table('anomaly_logs', function (Blueprint $table) {
            $table->index('attempted_at');
        });
    }

    public function down(): void
    {
        Schema::table('anomaly_logs', function (Blueprint $table) {
            $table->dropIndex(['attempted_at']);
        });

        Schema::table('meal_logs', function (Blueprint $table) {
            $table->dropIndex(['served_at']);
            $table->dropIndex(['project_id', 'served_at']);
        });
    }
};