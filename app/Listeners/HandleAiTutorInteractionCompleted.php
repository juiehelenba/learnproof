<?php

namespace App\Listeners;

use App\Events\AiTutorInteractionCompleted;
use App\Jobs\NotifyStudentAboutAiTutorFallback;
use Illuminate\Support\Facades\Log;

class HandleAiTutorInteractionCompleted
{
    public function handle(AiTutorInteractionCompleted $event): void
    {
        $interaction = $event->interaction->loadMissing(['user', 'course']);

        Log::info('learnproof.ai.tutor.interaction_handled', [
            'interaction_id' => $interaction->id,
            'status' => $interaction->status,
            'used_fallback' => $interaction->used_fallback,
        ]);

        if ($interaction->used_fallback) {
            NotifyStudentAboutAiTutorFallback::dispatch($interaction);
        }
    }
}
