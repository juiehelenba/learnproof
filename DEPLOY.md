# Deploy e produção — LearnProof

Guia operacional para colocar o LearnProof em um ambiente real (VPS, Forge, shared hosting com SSH, ou container).

Documentação de produto: [`PROJETO.md`](PROJETO.md) · Demo local: `php artisan learnproof:demo`

---

## 1. Checklist antes do go-live

| Item | Comando / ação | Esperado |
|------|----------------|----------|
| PHP 8.3+ | `php -v` | ≥ 8.3 |
| Extensões | `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath` | OK |
| Node 18+ (build) | `node -v` | build do Vite |
| `.env` de produção | copiar de `.env.production.example` | `APP_ENV=production`, `APP_DEBUG=false` |
| `APP_KEY` | `php artisan key:generate --force` | chave definida |
| Migrações | `php artisan migrate --force` | sem pending |
| Assets | `npm ci && npm run build` | `public/build` |
| Fila | `php artisan queue:work` (systemd/supervisor) | jobs de blockchain/IA |
| Saúde | `GET /up` e `GET /health` | 200 / JSON `status` ok ou degraded |
| Demo checklist | `php artisan learnproof:demo` | itens verdes (IA/blockchain conforme escopo) |

Verificação rápida:

```bash
composer run prod:check
```

---

## 2. Variáveis essenciais

Use [`.env.production.example`](.env.production.example) como base.

### App

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
LOG_LEVEL=warning
```

### Banco (MySQL recomendado)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learnproof
DB_USERNAME=learnproof
DB_PASSWORD=********
```

Não use SQLite em produção compartilhada/concorrente.

### Fila e cache

```env
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

Com Redis disponível:

```env
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

### IA

```env
AI_ENABLED=true
OPENAI_API_KEY=sk-...
AI_MAX_INTERACTIONS_PER_DAY=40
AI_MAX_COST_USD_PER_DAY=0.50
```

Sem chave, o tutor cai em fallback (e notifica o aluno).

### Blockchain

```env
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_MODE=evm          # ou mock só em staging
BLOCKCHAIN_NETWORK=polygon-amoy
BLOCKCHAIN_RPC_URL=https://...
BLOCKCHAIN_CONTRACT_ADDRESS=0x...
BLOCKCHAIN_WALLET_PRIVATE_KEY=0x...   # NUNCA commitar
```

Deploy do contrato (uma vez):

```bash
cd blockchain && npm install
php artisan blockchain:setup --deploy
```

A ancoragem real exige worker de fila (`queue:work` / `queue:listen`).

---

## 3. Passos de deploy (VPS / Forge)

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
cp .env.production.example .env   # só na primeira vez; depois edite o .env existente
# preencha secrets (DB, OpenAI, blockchain)
php artisan key:generate --force  # só se APP_KEY vazia
php artisan migrate --force
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Supervisor (exemplo de programa):

```ini
[program:learnproof-worker]
command=php /var/www/learnproof/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/learnproof/storage/logs/worker.log
```

Document root do Nginx/Apache: pasta `public/`.

---

## 4. Monitoramento

| Endpoint | Uso |
|----------|-----|
| `GET /up` | Health mínimo do Laravel (load balancer) |
| `GET /health` | JSON com DB, cache, fila, IA e blockchain (sem secrets) |
| `/instrutor/metricas` | Painel staff (fallback IA, certificados, custo) |
| `php artisan learnproof:metrics --days=7` | CLI / export CSV com `--csv=` |

Alertas sugeridos:

- `/health` com `status=down` → página / pager
- `failed_jobs` > 0 → investigar ancoragem blockchain
- taxa de fallback IA alta → revisar `OPENAI_API_KEY` / cotas

---

## 5. Segurança

- `APP_DEBUG=false` em produção
- Não versionar `.env`, chaves privadas nem `blockchain/.env`
- HTTPS obrigatório (`APP_URL` com `https://`)
- Rotacionar `OPENAI_API_KEY` e wallet se vazarem
- Rate limits já existem no chat web/API e no quiz
- Limites diários de IA: `AI_MAX_INTERACTIONS_PER_DAY` / `AI_MAX_COST_USD_PER_DAY`

---

## 6. Staging vs produção

| | Staging | Produção |
|--|---------|----------|
| `BLOCKCHAIN_MODE` | `mock` ou Amoy testnet | rede acordada (Amoy ou mainnet) |
| `APP_DEBUG` | pode `true` | `false` |
| Seeders | `db:seed` OK | evite `migrate:fresh` |
| Contas demo | úteis | desative ou troque senhas |

---

## 7. Rollback rápido

```bash
git checkout <commit-anterior>
composer install --no-dev --optimize-autoloader
php artisan migrate --force   # só se houver migrations reversíveis
npm ci && npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan queue:restart
```

Se a migration não for reversível, restaure backup do banco antes.

---

## 8. Contatos úteis no código

| Recurso | Onde |
|---------|------|
| Health detalhado | `app/Http/Controllers/HealthController.php` |
| Métricas | `app/Services/MetricsService.php` |
| Ancoragem | `app/Services/BlockchainAnchorService.php` + Job |
| Limite IA | `app/Services/Ai/AiUsageLimiter.php` |
| Checklist demo | `php artisan learnproof:demo` |
| Prod check | `composer run prod:check` | config + migrations + health |
