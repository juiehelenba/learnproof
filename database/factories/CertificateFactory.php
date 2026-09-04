<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        $uuid = (string) Str::uuid();
        $issuedAt = now();
        $user = User::factory();
        $course = Course::factory();

        return [
            'user_id' => $user,
            'course_id' => $course,
            'uuid' => $uuid,
            // Placeholder — recalculado em configure() quando IDs reais existem.
            'content_hash' => str_repeat('0', 64),
            'blockchain_tx_hash' => '0x'.Str::lower(Str::random(64)),
            'blockchain_network' => 'mock-polygon-amoy',
            'issued_at' => $issuedAt,
            'metadata' => [
                'student_name' => 'Aluno Teste',
                'course_title' => 'Curso Teste',
                'quiz_score' => 100,
                'issuer' => 'LearnProof',
            ],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Certificate $certificate) {
            $certificate->content_hash = Certificate::contentHashFor(
                $certificate->uuid,
                (int) $certificate->user_id,
                (int) $certificate->course_id,
                $certificate->issued_at,
            );
        })->afterCreating(function (Certificate $certificate) {
            $certificate->loadMissing(['user', 'course']);

            $certificate->update([
                'content_hash' => Certificate::contentHashFor(
                    $certificate->uuid,
                    $certificate->user_id,
                    $certificate->course_id,
                    $certificate->issued_at,
                ),
                'metadata' => array_merge([
                    'student_name' => $certificate->user->name,
                    'course_title' => $certificate->course->title,
                    'quiz_score' => 100,
                    'issuer' => 'LearnProof',
                ], $certificate->metadata ?? []),
            ]);
        });
    }

    public function simulated(): static
    {
        return $this->state(fn () => [
            'blockchain_network' => 'mock-polygon-amoy',
            'blockchain_tx_hash' => '0x'.Str::lower(Str::random(64)),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'blockchain_network' => 'polygon-amoy',
            'blockchain_tx_hash' => null,
        ]);
    }
}
