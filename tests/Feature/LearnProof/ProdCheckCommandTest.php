<?php

test('learnproof:prod-check exibe checklist de produção', function () {
    $this->artisan('learnproof:prod-check')
        ->expectsOutputToContain('verificação de produção')
        ->expectsOutputToContain('APP_KEY definida')
        ->expectsOutputToContain('Health')
        ->expectsOutputToContain('/health');
});
