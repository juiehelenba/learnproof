<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->index(['course_id', 'sort_order'], 'lessons_course_id_sort_order_index');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'quiz_id', 'created_at'], 'quiz_attempts_user_quiz_created_index');
            $table->index(['quiz_id', 'passed'], 'quiz_attempts_quiz_id_passed_index');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->index('issued_at', 'certificates_issued_at_index');
            $table->index('blockchain_tx_hash', 'certificates_blockchain_tx_hash_index');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('lessons_course_id_sort_order_index');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex('quiz_attempts_user_quiz_created_index');
            $table->dropIndex('quiz_attempts_quiz_id_passed_index');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex('certificates_issued_at_index');
            $table->dropIndex('certificates_blockchain_tx_hash_index');
        });
    }
};
