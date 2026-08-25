<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lecture_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecture_id')->constrained()->cascadeOnDelete();
            $table->timestamp('first_viewed_at')->useCurrent();
            $table->timestamp('last_viewed_at')->useCurrent();
            $table->unsignedInteger('view_count')->default(1);
            $table->timestamp('downloaded_at')->nullable();
            $table->unsignedSmallInteger('download_count')->default(0);
            $table->timestamps();

            // One row per (user, lecture) — view_count tracks repeat reads
            $table->unique(['user_id', 'lecture_id']);
            $table->index('lecture_id');
            $table->index(['user_id', 'last_viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecture_views');
    }
};
