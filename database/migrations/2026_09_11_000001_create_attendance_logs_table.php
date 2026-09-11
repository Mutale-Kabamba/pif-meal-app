<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('recorded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activity_type', 50)->default('session'); // training | class_session | session
            $table->date('attended_at');
            $table->string('status', 20)->default('present'); // present | absent
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['beneficiary_id', 'project_id', 'attended_at'], 'unique_daily_attendance');
            $table->index(['project_id', 'attended_at']);
            $table->index(['team_id', 'attended_at']);
            $table->index(['recorded_by_user_id', 'attended_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
