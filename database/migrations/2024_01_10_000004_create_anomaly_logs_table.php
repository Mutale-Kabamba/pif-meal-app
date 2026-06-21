<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('anomaly_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('served_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'standard_ration'])->default('standard_ration');
            $table->timestamp('attempted_at');
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_logs');
    }
};
