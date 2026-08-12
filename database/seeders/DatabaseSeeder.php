<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@learnproof.test'],
            [
                'name' => 'Admin LearnProof',
                'role' => UserRole::Admin,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'instrutor@learnproof.test'],
            [
                'name' => 'Instrutor',
                'role' => UserRole::Instructor,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $student = User::query()->updateOrCreate(
            ['email' => 'aluno@learnproof.test'],
            [
                'name' => 'Aluno',
                'role' => UserRole::Student,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call(LearnProofSeeder::class);

        $course = Course::query()->where('slug', 'fundamentos-ia-generativa')->first();

        if ($course) {
            Enrollment::query()->firstOrCreate(
                [
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                ],
                [
                    'enrolled_at' => now(),
                ]
            );
        }
    }
}
