<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'description' => fake()->paragraph(),
            'thumbnail' => null,
            'passing_score' => 70,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    /**
     * Curso completo: aulas + quiz com questões (1 correta + 1 incorreta cada).
     */
    public function withCurriculum(int $lessons = 2, int $questions = 2): static
    {
        return $this->afterCreating(function (Course $course) use ($lessons, $questions) {
            for ($i = 1; $i <= $lessons; $i++) {
                Lesson::factory()->create([
                    'course_id' => $course->id,
                    'sort_order' => $i,
                    'title' => "Aula {$i}",
                    'slug' => "aula-{$i}-{$course->id}",
                ]);
            }

            $quiz = Quiz::factory()->create([
                'course_id' => $course->id,
                'passing_score' => $course->passing_score,
                'title' => 'Avaliação final — '.$course->title,
            ]);

            for ($i = 1; $i <= $questions; $i++) {
                $question = Question::factory()->create([
                    'quiz_id' => $quiz->id,
                    'sort_order' => $i,
                    'text' => "Pergunta {$i}?",
                ]);

                QuestionOption::factory()->incorrect()->create([
                    'question_id' => $question->id,
                    'text' => 'Resposta incorreta',
                ]);

                QuestionOption::factory()->correct()->create([
                    'question_id' => $question->id,
                    'text' => 'Resposta correta',
                ]);
            }
        });
    }
}
