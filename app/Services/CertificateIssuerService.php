<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CertificateIssuerService
{
    public function __construct(
        private BlockchainAnchorService $blockchain,
    ) {}

    public function issue(User $user, Course $course, int $quizScore): Certificate
    {
        [$certificate, $isNew] = $this->firstOrCreate($user, $course, $quizScore);

        // Ancorar apenas na primeira emissão evita gastar uma transação
        // on-chain a cada reenvio do quiz.
        if ($isNew) {
            $this->blockchain->queueAnchor($certificate);
        }

        return $certificate->refresh();
    }

    /**
     * @return array{0: Certificate, 1: bool}
     */
    private function firstOrCreate(User $user, Course $course, int $quizScore): array
    {
        if ($existing = $this->findFor($user, $course)) {
            return [$existing, false];
        }

        try {
            return [$this->create($user, $course, $quizScore), true];
        } catch (UniqueConstraintViolationException $e) {
            // Dois submits concorrentes: o outro processo emitiu primeiro.
            $existing = $this->findFor($user, $course);

            if (! $existing) {
                throw new RuntimeException(
                    "Falha ao emitir certificado do curso {$course->id} para o usuário {$user->id}.",
                    previous: $e,
                );
            }

            return [$existing, false];
        }
    }

    private function findFor(User $user, Course $course): ?Certificate
    {
        return Certificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
    }

    private function create(User $user, Course $course, int $quizScore): Certificate
    {
        return DB::transaction(function () use ($user, $course, $quizScore) {
            $uuid = (string) Str::uuid();
            $issuedAt = now();

            return Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'uuid' => $uuid,
                'content_hash' => Certificate::contentHashFor(
                    $uuid,
                    $user->id,
                    $course->id,
                    $issuedAt,
                ),
                'blockchain_network' => config('learnproof.blockchain.network'),
                'issued_at' => $issuedAt,
                'metadata' => [
                    'student_name' => $user->name,
                    'course_title' => $course->title,
                    'quiz_score' => $quizScore,
                    'issuer' => config('learnproof.name'),
                ],
            ]);
        });
    }
}
