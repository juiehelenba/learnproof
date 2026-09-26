<?php

use App\Events\CertificateAnchored;
use App\Models\Certificate;
use App\Models\User;
use App\Notifications\CertificateAnchoredNotification;
use App\Services\BlockchainAnchorService;
use App\Services\CertificateIssuerService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

test('ancoragem mock dispara evento e notifica o aluno', function () {
    Event::fake([CertificateAnchored::class]);
    Notification::fake();

    config([
        'learnproof.blockchain.enabled' => true,
        'learnproof.blockchain.mode' => 'mock',
    ]);

    $user = User::factory()->create();
    $certificate = Certificate::factory()->for($user)->pending()->create([
        'blockchain_tx_hash' => null,
    ]);

    $txHash = app(BlockchainAnchorService::class)->anchorOnChain($certificate);

    expect($txHash)->toStartWith('0x')
        ->and($certificate->fresh()->isSimulatedAnchor())->toBeTrue();

    Event::assertDispatched(CertificateAnchored::class, function (CertificateAnchored $event) use ($certificate) {
        return $event->certificate->is($certificate->fresh());
    });
});

test('listener grava notificação database ao ancorar', function () {
    Notification::fake();

    config([
        'learnproof.blockchain.enabled' => true,
        'learnproof.blockchain.mode' => 'mock',
    ]);

    $user = User::factory()->create();
    $certificate = Certificate::factory()->for($user)->create([
        'blockchain_tx_hash' => null,
        'blockchain_network' => 'polygon-amoy',
    ]);

    app(BlockchainAnchorService::class)->queueAnchor($certificate);

    Notification::assertSentTo(
        $user,
        CertificateAnchoredNotification::class,
        function (CertificateAnchoredNotification $notification) use ($certificate) {
            $payload = $notification->toArray($certificate->user);

            return $payload['type'] === 'certificate_anchored'
                && $payload['certificate_uuid'] === $certificate->fresh()->uuid
                && $payload['simulated'] === true;
        }
    );
});

test('emissão de certificado em modo mock ainda notifica ancoragem', function () {
    Notification::fake();

    config([
        'learnproof.blockchain.enabled' => true,
        'learnproof.blockchain.mode' => 'mock',
    ]);

    $user = User::factory()->create();
    $course = \App\Models\Course::factory()->create();

    $certificate = app(CertificateIssuerService::class)->issue($user, $course, 95);

    expect($certificate->blockchain_tx_hash)->not->toBeNull();

    Notification::assertSentTo($user, CertificateAnchoredNotification::class);
});
