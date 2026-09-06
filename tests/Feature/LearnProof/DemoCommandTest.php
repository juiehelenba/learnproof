<?php

use App\Services\DemoReadinessService;

test('DemoReadinessService retorna checks e roteiro sem expor segredos', function () {
    $report = app(DemoReadinessService::class)->assess();

    expect($report)->toHaveKeys(['ready', 'checks', 'accounts', 'course', 'script'])
        ->and($report['script'])->not->toBeEmpty()
        ->and($report['checks'])->not->toBeEmpty();

    $encoded = json_encode($report);

    expect($encoded)
        ->not->toContain('sk-')
        ->not->toContain('private_key')
        ->not->toContain('BLOCKCHAIN_WALLET');
});

test('learnproof:demo lista o roteiro de apresentação', function () {
    $this->artisan('learnproof:demo')
        ->expectsOutputToContain('Roteiro sugerido')
        ->expectsOutputToContain('aluno@learnproof.test')
        ->assertExitCode(
            app(DemoReadinessService::class)->assess()['ready'] ? 0 : 1
        );
});
