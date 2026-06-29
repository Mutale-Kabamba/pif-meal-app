<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('teams'); // clean up any orphaned table from a prior failed run

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // MySQL only: extend the role enum to include 'coach'.
        // SQLite stores enums as TEXT so no ALTER is needed.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('head_of_programmes','system_manager','project_officer','cook','coach') DEFAULT 'cook'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('head_of_programmes','system_manager','project_officer','cook') DEFAULT 'cook'");
        }
    }
};
