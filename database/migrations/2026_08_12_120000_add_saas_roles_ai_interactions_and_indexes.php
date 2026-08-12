<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('student')->after('email');
            $table->index('role');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index('is_published');
            $table->index(['is_published', 'slug']);
        });

        Schema::table('ai_chat_messages', function (Blueprint $table) {
            $table->string('provider', 32)->nullable()->after('content');
            $table->string('model', 64)->nullable()->after('provider');
            $table->boolean('used_fallback')->default(false)->after('model');
            $table->unsignedInteger('latency_ms')->nullable()->after('used_fallback');
            $table->unsignedInteger('prompt_tokens')->nullable()->after('latency_ms');
            $table->unsignedInteger('completion_tokens')->nullable()->after('prompt_tokens');
            $table->json('meta')->nullable()->after('completion_tokens');

            $table->index(['user_id', 'course_id', 'created_at']);
            $table->index(['course_id', 'created_at']);
        });

        Schema::create('ai_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_message_id')->nullable()->constrained('ai_chat_messages')->nullOnDelete();
            $table->foreignId('assistant_message_id')->nullable()->constrained('ai_chat_messages')->nullOnDelete();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->string('status', 32)->default('completed');
            $table->string('provider', 32)->nullable();
            $table->string('model', 64)->nullable();
            $table->boolean('used_fallback')->default(false);
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('context_chars')->nullable();
            $table->json('context_snapshot')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'course_id', 'created_at']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_interactions');

        Schema::table('ai_chat_messages', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'course_id', 'created_at']);
            $table->dropIndex(['course_id', 'created_at']);
            $table->dropColumn([
                'provider',
                'model',
                'used_fallback',
                'latency_ms',
                'prompt_tokens',
                'completion_tokens',
                'meta',
            ]);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
            $table->dropIndex(['is_published', 'slug']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
