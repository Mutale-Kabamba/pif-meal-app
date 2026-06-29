<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Meals are now attributed to the cook's assigned project, not the beneficiary's project.
        // Make project_id nullable to handle edge-case cooks without a project assignment.
        Schema::table('meal_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });

        Schema::table('anomaly_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('meal_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });

        Schema::table('anomaly_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }
};
