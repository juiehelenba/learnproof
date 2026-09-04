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
        return $this->lessonProgress()->count();
    }

    public function progressPercent(): int
    {
        $total = $this->course->lessons()->count();

        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->completedLessonsCount() / $total) * 100);
    }

    public function allLessonsCompleted(): bool
    {
        return $this->completedLessonsCount() >= $this->course->lessons()->count();
    }
}
