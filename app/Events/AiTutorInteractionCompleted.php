<?php

namespace App\Events;

use App\Models\AiInteraction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AiTutorInteractionCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public AiInteraction $interaction,
    ) {}
}
