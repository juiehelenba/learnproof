<?php

use Illuminate\Support\Facades\Schema;

test('índices de performance do LearnProof existem após as migrations', function () {
    $lessonIndexes = collect(Schema::getIndexes('lessons'))->pluck('name');
    $attemptIndexes = collect(Schema::getIndexes('quiz_attempts'))->pluck('name');
    $certificateIndexes = collect(Schema::getIndexes('certificates'))->pluck('name');

    expect($lessonIndexes)->toContain('lessons_course_id_sort_order_index')
        ->and($attemptIndexes)->toContain('quiz_attempts_user_quiz_created_index')
        ->and($attemptIndexes)->toContain('quiz_attempts_quiz_id_passed_index')
        ->and($certificateIndexes)->toContain('certificates_issued_at_index')
        ->and($certificateIndexes)->toContain('certificates_blockchain_tx_hash_index');
});
