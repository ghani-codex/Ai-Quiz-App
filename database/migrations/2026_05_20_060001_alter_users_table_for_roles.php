<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email');
            $table->boolean('is_approved')->default(true)->after('role');
            $table->string('avatar_path')->nullable()->after('is_approved');
            $table->text('bio')->nullable()->after('avatar_path');
            $table->string('country', 80)->nullable()->after('bio');

            $table->index(['role', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_approved']);
            $table->dropColumn(['role', 'is_approved', 'avatar_path', 'bio', 'country']);
        });
    }
};
