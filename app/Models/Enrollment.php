<?php

namespace App\Models;

use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'completed_at',
    ];

    private ?int $memoCompletedCount = null;

    private ?int $memoLessonsCount = null;

    private ?int $memoProgressPercent = null;

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function completedLessonsCount(): int
    {
        if ($this->memoCompletedCount !== null) {
            return $this->memoCompletedCount;
        }

        if (array_key_exists('lesson_progress_count', $this->attributes)) {
            return $this->memoCompletedCount = (int) $this->attributes['lesson_progress_count'];
        }

        if ($this->relationLoaded('lessonProgress')) {
            return $this->memoCompletedCount = $this->lessonProgress->count();
        }

        return $this->memoCompletedCount = $this->lessonProgress()->count();
    }

    public function lessonsCount(): int
    {
        if ($this->memoLessonsCount !== null) {
            return $this->memoLessonsCount;
        }

        $course = $this->course;

        if ($course && array_key_exists('lessons_count', $course->getAttributes())) {
            return $this->memoLessonsCount = (int) $course->lessons_count;
        }

        if ($course && $course->relationLoaded('lessons')) {
            return $this->memoLessonsCount = $course->lessons->count();
        }

        return $this->memoLessonsCount = $course?->lessons()->count() ?? 0;
    }

    public function progressPercent(): int
    {
        if ($this->memoProgressPercent !== null) {
            return $this->memoProgressPercent;
        }

        $total = $this->lessonsCount();

        if ($total === 0) {
            return $this->memoProgressPercent = 0;
        }

        return $this->memoProgressPercent = (int) round(($this->completedLessonsCount() / $total) * 100);
    }

    public function allLessonsCompleted(): bool
    {
        return $this->completedLessonsCount() >= $this->lessonsCount();
    }
}
