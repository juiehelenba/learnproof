<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;

class TeamInitiativeGuide
{
    /**
     * @return array{
     *     invite_message: string,
     *     author_steps: list<string>,
     *     organizer_steps: list<string>,
     *     stats: array{published: int, draft: int, enrollments: int}
     * }
     */
    public function forPanel(): array
    {
        return [
            'invite_message' => trim((string) config('learnproof.team.invite_message')),
            'author_steps' => config('learnproof.team.author_steps', []),
            'organizer_steps' => config('learnproof.team.organizer_steps', []),
            'stats' => [
                'published' => Course::query()->where('is_published', true)->count(),
                'draft' => Course::query()->where('is_published', false)->count(),
                'enrollments' => Enrollment::query()->count(),
            ],
        ];
    }
}
