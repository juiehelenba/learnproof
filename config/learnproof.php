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
        // Estimativa de custo USD por 1M tokens (gpt-4o-mini aproximado).
        'price_prompt_per_1m' => (float) env('AI_PRICE_PROMPT_PER_1M', 0.15),
        'price_completion_per_1m' => (float) env('AI_PRICE_COMPLETION_PER_1M', 0.60),
        // 0 desativa o limite. Protege custo da API OpenAI por aluno/dia.
        'max_interactions_per_day' => (int) env('AI_MAX_INTERACTIONS_PER_DAY', 40),
        'max_estimated_cost_usd_per_day' => (float) env('AI_MAX_COST_USD_PER_DAY', 0.50),
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

    /*
    | Iniciativa interna "Cada um ensina" — textos do painel /instrutor/equipe
    */
    'team' => [
        'invite_message' => <<<'TEXT'
Pessoal, estou montando uma biblioteca de microconteúdos na plataforma LearnProof: cada um escolhe um tema que domina, escreve ou grava em cerca de 20 minutos, e publicamos com quiz no final. A ideia é disponibilizar para alunos como material complementar. Quem topa ser voluntário nos primeiros temas? Eu apoio na publicação e na montagem da avaliação.
TEXT,
        'author_steps' => [
            'Escolha um tema que você explica bem no dia a dia.',
            'Produza o conteúdo (Markdown na plataforma, vídeo externo ou bullet points).',
            'Combine com o organizador a nota mínima e o número de questões do quiz.',
            'Revise o quiz em ~10 minutos quando receber o rascunho.',
            'Após publicação, compartilhe o link do curso com quem for consumir.',
        ],
        'organizer_steps' => [
            'Crie o curso como rascunho no painel (Novo curso).',
            'Cadastre aulas na ordem sugerida e a avaliação final com questões.',
            'Peça ao autor revisar enunciados e alternativas corretas.',
            'Publique o curso quando conteúdo e quiz estiverem validados.',
            'Acompanhe matrículas, conclusões e tutor de IA em Métricas.',
            'Antes de apresentar à gestão, rode: php artisan learnproof:demo',
        ],
    ],
];
