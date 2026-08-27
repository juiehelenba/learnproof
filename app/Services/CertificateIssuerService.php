<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Str;

class CertificateIssuerService
{
    public function __construct(
        private BlockchainAnchorService $blockchain,
    ) {}

    public function issue(User $user, Course $course, int $quizScore): Certificate
    {
        $existing = Certificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $uuid = (string) Str::uuid();
        $issuedAt = now();

        $contentHash = hash('sha256', implode('|', [
            $uuid,
            $user->id,
            $course->id,
            $issuedAt->toIso8601String(),
        ]));

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'uuid' => $uuid,
            'content_hash' => $contentHash,
            'blockchain_network' => config('learnproof.blockchain.network'),
            'issued_at' => $issuedAt,
            'metadata' => [
                'student_name' => $user->name,
                'course_title' => $course->title,
                'quiz_score' => $quizScore,
                'issuer' => config('learnproof.name'),
            ],
        ]);

        $this->blockchain->queueAnchor($certificate);

        return $certificate->fresh();
    }
}
