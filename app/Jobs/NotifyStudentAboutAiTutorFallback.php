<?php

namespace App\Jobs;

use App\Models\AiInteraction;
use App\Notifications\AiTutorFallbackNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyStudentAboutAiTutorFallback implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AiInteraction $interaction,
    ) {}

    public function handle(): void
    {
        $interaction = $this->interaction->loadMissing(['user', 'course']);

        $interaction->user->notify(
            new AiTutorFallbackNotification($interaction)
        );
    }
}
