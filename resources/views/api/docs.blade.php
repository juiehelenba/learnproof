<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('learnproof.name') }} — API Docs</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui.css">
    <style>
        body { margin: 0; background: #fafafa; }
        .topbar { display: none; }
        .lp-header {
            padding: 1rem 1.5rem;
            background: #312e81;
            color: #fff;
            font-family: system-ui, sans-serif;
        }
        .lp-header a { color: #c7d2fe; }
    </style>
</head>
<body>
    <header class="lp-header">
        <strong>{{ config('learnproof.name') }} API v1</strong>
        · <a href="{{ $specUrl }}">openapi.yaml</a>
        · <a href="{{ route('api.v1.index') }}">índice JSON</a>
    </header>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-bundle.js"></script>
    <script>
        window.ui = SwaggerUIBundle({
            url: @json($specUrl),
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [SwaggerUIBundle.presets.apis],
            layout: 'BaseLayout',
        });
    </script>
</body>
</html>
