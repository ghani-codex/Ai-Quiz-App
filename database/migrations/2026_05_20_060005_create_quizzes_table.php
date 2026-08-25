<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->unsignedTinyInteger('question_count')->default(10);
            $table->unsignedSmallInteger('time_limit_minutes')->default(15);
            $table->boolean('is_published')->default(false);
            $table->enum('ai_generation_status', ['pending', 'processing', 'ready', 'failed'])->default('pending');
            $table->string('ai_provider_used', 20)->nullable();
            $table->unsignedInteger('ai_tokens_used')->nullable();
            $table->timestamp('ai_generated_at')->nullable();
            $table->timestamps();

            $table->index('ai_generation_status');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
