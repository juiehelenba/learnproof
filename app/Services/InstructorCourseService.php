<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InstructorCourseService
{
    /**
     * @param  array{title: string, slug?: ?string, description: string, passing_score: int, is_published?: bool}  $data
     */
    public function createCourse(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $slug = $this->uniqueCourseSlug($data['slug'] ?? null, $data['title']);

            $course = Course::query()->create([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'],
                'passing_score' => $data['passing_score'],
                'is_published' => $this->asBool($data['is_published'] ?? false),
            ]);

            Quiz::query()->create([
                'course_id' => $course->id,
                'title' => 'Avaliação final — '.$course->title,
                'passing_score' => $data['passing_score'],
            ]);

            return $course->fresh('quiz');
        });
    }

    /**
     * @param  array{title: string, slug: string, description: string, passing_score: int, is_published?: bool}  $data
     */
    public function updateCourse(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {
            $course->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'passing_score' => $data['passing_score'],
                'is_published' => array_key_exists('is_published', $data)
                    ? $this->asBool($data['is_published'])
                    : $course->is_published,
            ]);

            $course->quiz()?->update([
                'passing_score' => $data['passing_score'],
            ]);

            return $course->fresh('quiz');
        });
    }

    /**
     * @param  array{title: string, slug?: ?string, content: string, duration_minutes: int, sort_order?: ?int, video_url?: ?string}  $data
     */
    public function createLesson(Course $course, array $data): Lesson
    {
        $sortOrder = $data['sort_order'] ?? (($course->lessons()->max('sort_order') ?? 0) + 1);

        return Lesson::query()->create([
            'course_id' => $course->id,
            'title' => $data['title'],
            'slug' => $this->uniqueLessonSlug($course, $data['slug'] ?? null, $data['title']),
            'content' => $data['content'],
            'duration_minutes' => $data['duration_minutes'],
            'sort_order' => $sortOrder,
            'video_url' => $data['video_url'] ?? null,
        ]);
    }

    /**
     * @param  array{title: string, slug: string, content: string, duration_minutes: int, sort_order: int, video_url?: ?string}  $data
     */
    public function updateLesson(Lesson $lesson, array $data): Lesson
    {
        $lesson->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'duration_minutes' => $data['duration_minutes'],
            'sort_order' => $data['sort_order'],
            'video_url' => $data['video_url'] ?? null,
        ]);

        return $lesson->fresh();
    }

    /**
     * @param  array{title: string, passing_score: int}  $data
     */
    public function updateQuiz(Course $course, array $data): Quiz
    {
        $quiz = $course->quiz;

        if (! $quiz) {
            $quiz = Quiz::query()->create([
                'course_id' => $course->id,
                'title' => $data['title'],
                'passing_score' => $data['passing_score'],
            ]);
        } else {
            $quiz->update($data);
        }

        $course->update(['passing_score' => $data['passing_score']]);

        return $quiz->fresh();
    }

    /**
     * @param  array{text: string, explanation?: ?string, sort_order?: ?int, options: array<int, array{text: string}>, correct_option: int}  $data
     */
    public function createQuestion(Quiz $quiz, array $data): Question
    {
        return DB::transaction(function () use ($quiz, $data) {
            $sortOrder = $data['sort_order'] ?? (($quiz->questions()->max('sort_order') ?? 0) + 1);

            $question = Question::query()->create([
                'quiz_id' => $quiz->id,
                'text' => $data['text'],
                'explanation' => $data['explanation'] ?? null,
                'sort_order' => $sortOrder,
            ]);

            foreach ($data['options'] as $index => $option) {
                QuestionOption::query()->create([
                    'question_id' => $question->id,
                    'text' => $option['text'],
                    'is_correct' => (int) $data['correct_option'] === (int) $index,
                ]);
            }

            return $question->fresh('options');
        });
    }

    public function deleteQuestion(Question $question): void
    {
        $question->delete();
    }

    private function uniqueCourseSlug(?string $slug, string $title): string
    {
        $base = Str::slug($slug ?: $title) ?: 'curso';
        $candidate = $base;
        $i = 2;

        while (Course::query()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.$i;
            $i++;
        }

        return $candidate;
    }

    private function uniqueLessonSlug(Course $course, ?string $slug, string $title): string
    {
        $base = Str::slug($slug ?: $title) ?: 'aula';
        $candidate = $base;
        $i = 2;

        while (
            Lesson::query()
                ->where('course_id', $course->id)
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = $base.'-'.$i;
            $i++;
        }

        return $candidate;
    }

    private function asBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
