<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        if ($course->is_published) {
            return true;
        }

        return $user?->role?->isStaff() ?? false;
    }

    public function create(User $user): bool
    {
        return $user->role?->isStaff() ?? false;
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role?->isStaff() ?? false;
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function enroll(User $user, Course $course): bool
    {
        return $course->is_published && $user->role !== null;
    }

    public function useAiTutor(User $user, Course $course): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->enrollmentFor($course) !== null;
    }
}
