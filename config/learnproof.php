<?php

return [
    'name' => env('LEARNPROOF_NAME', 'LearnProof'),

    'ai' => [
        'enabled' => env('AI_ENABLED', true),
        'provider' => env('AI_PROVIDER', 'openai'),
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'max_history' => (int) env('AI_MAX_HISTORY', 20),
        'timeout' => (int) env('AI_TIMEOUT', 30),
        'context_cache_ttl' => (int) env('AI_CONTEXT_CACHE_TTL', 600),
        'lesson_excerpt_chars' => (int) env('AI_LESSON_EXCERPT_CHARS', 1200),
    ],

    'blockchain' => [
        'enabled' => env('BLOCKCHAIN_ENABLED', true),
        'mode' => env('BLOCKCHAIN_MODE', 'mock'),
        'network' => env('BLOCKCHAIN_NETWORK', 'polygon-amoy'),
        'rpc_url' => env('BLOCKCHAIN_RPC_URL'),
        'contract_address' => env('BLOCKCHAIN_CONTRACT_ADDRESS'),
        'wallet_private_key' => env('BLOCKCHAIN_WALLET_PRIVATE_KEY'),
        'explorer_tx_url' => env('BLOCKCHAIN_EXPLORER_TX_URL', 'https://amoy.polygonscan.com/tx/%s'),
        'timeout' => (int) env('BLOCKCHAIN_TIMEOUT', 120),

        // Evita que a página pública de verificação dispare um processo Node
        // por acesso. Zero desativa o cache.
        'verify_cache_ttl' => (int) env('BLOCKCHAIN_VERIFY_CACHE_TTL', 300),
    ],

    'quiz' => [
        'max_attempts_per_hour' => (int) env('QUIZ_MAX_ATTEMPTS_PER_HOUR', 5),
    ],

    'certificate' => [
        // Fallback de nota mínima quando o quiz não define a sua.
        'min_quiz_score' => (int) env('CERTIFICATE_MIN_SCORE', 70),
    ],
];
