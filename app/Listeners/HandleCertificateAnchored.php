<?php

namespace App\Listeners;

use App\Events\CertificateAnchored;
use App\Notifications\CertificateAnchoredNotification;
use Illuminate\Support\Facades\Log;

class HandleCertificateAnchored
{
    public function handle(CertificateAnchored $event): void
    {
        $certificate = $event->certificate->loadMissing(['user', 'course']);

        Log::info('learnproof.blockchain.anchor_notified', [
            'uuid' => $certificate->uuid,
            'user_id' => $certificate->user_id,
            'network' => $certificate->blockchain_network,
            'simulated' => $certificate->isSimulatedAnchor(),
        ]);

        if ($certificate->user) {
            $certificate->user->notify(new CertificateAnchoredNotification($certificate));
        }
    }
}
