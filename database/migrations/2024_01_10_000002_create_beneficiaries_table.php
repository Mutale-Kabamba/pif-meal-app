<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('shortcode')->unique();
            $table->uuid('qr_token')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('shortcode');
            $table->index('qr_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
