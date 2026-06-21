<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('generated_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('file_path');
            $table->integer('total_cards');
            $table->string('generated_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_sheets');
    }
};