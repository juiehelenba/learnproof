# LearnProof

Plataforma SaaS de microcursos com quiz, certificado verificável, blockchain (mock/real) e tutor de IA contextualizado.

Documentação de produto: [`PROJETO.md`](PROJETO.md).

## Destaque — Tutor de IA (Course → Context → Tutor)

1. Aluno pergunta algo no contexto de um curso  
2. Sistema identifica o curso e monta o **contexto pedagógico** (aulas + descrição, com cache)  
3. Envia prompt + histórico para o modelo (OpenAI)  
4. Persiste mensagens, registra **interação** (`ai_interactions`), dispara event/job/notification e logs estruturados  

## Funcionalidades

- Catálogo, matrícula, progresso de aulas, quiz e certificado verificável
- **API REST versionada** em `/api/v1` (Sanctum)
- Roles: `admin`, `instructor`, `student` (policies + middleware)
- Form Requests, Services, Jobs, Events/Listeners, Notifications
- Cache de contexto do curso, tratamento JSON de exceções e logs estruturados

## Stack

| Camada | Tecnologia |
|--------|------------|
| Backend | PHP 8.3+, Laravel 13 |
| Auth Web | Laravel Breeze |
| Auth API | Laravel Sanctum |
| Frontend | Blade, Tailwind CSS, Vite |
| Banco | SQLite (dev) / MySQL ou PostgreSQL |
| Fila | Database queue |
| Testes | Pest PHP |

## Instalação

```bash
git clone https://github.com/juiehelenba/learnproof.git
cd learnproof

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install
npm run build
composer run dev
```

App: `http://localhost:8000`

Para MySQL/PostgreSQL, ajuste `DB_CONNECTION` e credenciais no `.env` antes do migrate.

## Contas demo

| Papel | E-mail | Senha |
|-------|--------|-------|
| Aluno (matriculado) | `aluno@learnproof.test` | `password` |
| Instrutor | `instrutor@learnproof.test` | `password` |
| Admin | `admin@learnproof.test` | `password` |

Curso demo: `/cursos/fundamentos-ia-generativa`

## API v1

Documentação interativa: [`/api/v1/docs`](http://localhost:8000/api/v1/docs) · Spec: [`/api/v1/openapi.yaml`](http://localhost:8000/api/v1/openapi.yaml)

Envelope padrão: `{ "data": ..., "meta": { "api_version": "v1" } }`. Listagens usam paginação Laravel (`data` + `links` + `meta`).

```bash
# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"aluno@learnproof.test","password":"password","device_name":"cli"}'

# Cursos (paginado)
curl "http://localhost:8000/api/v1/courses?per_page=15" -H "Accept: application/json"

# Tutor (Bearer token)
curl -X POST http://localhost:8000/api/v1/courses/fundamentos-ia-generativa/ai/chat \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"message":"O que é um prompt?"}'
```

Principais rotas:

| Método | Rota | Auth |
|--------|------|------|
| GET | `/api/v1` | — |
| GET | `/api/v1/docs` | — |
| POST | `/api/v1/login` | — |
| GET | `/api/v1/me` | Sanctum |
| POST | `/api/v1/logout` | Sanctum |
| GET | `/api/v1/courses` | — (staff vê rascunhos) |
| GET | `/api/v1/courses/{slug}` | — |
| GET | `/api/v1/courses/{slug}/ai/history` | Sanctum + matrícula |
| POST | `/api/v1/courses/{slug}/ai/chat` | Sanctum + matrícula |
| GET | `/api/v1/staff/ping` | Sanctum + staff |

## IA (opcional)

```env
AI_ENABLED=true
AI_PROVIDER=openai
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
AI_CONTEXT_CACHE_TTL=600
```

Sem chave, o tutor responde em **modo demonstração** e registra a interação mesmo assim.

## Licença

Projeto de portfólio — MIT.
