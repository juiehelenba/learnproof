# LearnProof

Plataforma de microcursos com quiz, certificado verificável, registro blockchain (mock/real) e tutor de IA contextualizado.

Documentação completa do produto: [`PROJETO.md`](PROJETO.md).

## Funcionalidades

- Catálogo de cursos, matrícula e progresso de aulas
- Quiz de fixação com nota mínima para aprovação
- Certificado com UUID e página pública de verificação
- Ancoragem blockchain do hash do certificado (modo mock por padrão)
- Tutor de IA por curso (OpenAI opcional; sem chave, modo demonstração)

## Stack

| Camada | Tecnologia |
|--------|------------|
| Backend | PHP 8.3+, Laravel 13 |
| Auth | Laravel Breeze |
| Frontend | Blade, Tailwind CSS, Vite |
| Banco | SQLite (padrão) ou MySQL |
| Testes | Pest PHP |

## Requisitos

- PHP >= 8.3
- Composer
- Node.js >= 18

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
```

Desenvolvimento (servidor + fila + logs + Vite):

```bash
composer run dev
```

App em `http://localhost:8000`.

## Conta de demonstração

| Campo | Valor |
|-------|-------|
| E-mail | `aluno@learnproof.test` |
| Senha | `password` |
| Curso demo | `/cursos/fundamentos-ia-generativa` |

## IA e blockchain (opcional)

No `.env` (veja também `.env.example`):

```env
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
BLOCKCHAIN_MODE=mock
```

Sem `OPENAI_API_KEY`, o tutor responde em modo demo.

## Licença

Projeto de portfólio — MIT.
