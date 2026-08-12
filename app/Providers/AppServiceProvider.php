<?php

namespace App\Providers;

use App\Events\AiTutorInteractionCompleted;
use App\Listeners\HandleAiTutorInteractionCompleted;
use App\Models\Course;
use App\Policies\CoursePolicy;
use App\Services\Ai\AiTutorService;
use App\Services\Ai\CourseContextBuilder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CourseContextBuilder::class);
        $this->app->singleton(AiTutorService::class);
    }

    public function boot(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);

        Event::listen(
            AiTutorInteractionCompleted::class,
            HandleAiTutorInteractionCompleted::class,
        );
    }
}
