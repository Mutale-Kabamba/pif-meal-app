<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('budget_code')->unique();
            $table->integer('daily_meal_limit_per_beneficiary')->default(1);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable(); // <-- ADD THIS LINE HERE
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};