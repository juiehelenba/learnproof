# LearnProof — Documento do Projeto

**Plataforma de microcursos com quiz, certificado verificável, blockchain e tutor de IA**

Versão: 1.0  
Data: maio/2026  
Autor: Julie (iniciativa colaborativa)

---

## Sumário

1. [Visão geral](#1-visão-geral)
2. [Contexto e motivação](#2-contexto-e-motivação)
3. [Proposta de valor](#3-proposta-de-valor)
4. [Iniciativa da equipe — “Cada um ensina”](#4-iniciativa-da-equipe--cada-um-ensina)
5. [Público-alvo](#5-público-alvo)
6. [Fluxos principais](#6-fluxos-principais)
7. [Funcionalidades da plataforma](#7-funcionalidades-da-plataforma)
8. [Arquitetura técnica](#8-arquitetura-técnica)
9. [Modelo de dados](#9-modelo-de-dados)
10. [Integrações — IA e Blockchain](#10-integrações--ia-e-blockchain)
11. [O que já está implementado](#11-o-que-já-está-implementado)
12. [Roadmap de evolução](#12-roadmap-de-evolução)
13. [Métricas e KPIs](#13-métricas-e-kpis)
14. [Aspectos legais e institucionais](#14-aspectos-legais-e-institucionais)
15. [Como apresentar à gestão](#15-como-apresentar-à-gestão)
16. [Guia operacional para a equipe](#16-guia-operacional-para-a-equipe)
17. [Como executar localmente](#17-como-executar-localmente)
18. [Anexos](#18-anexos)

---

## 1. Visão geral

**LearnProof** é uma plataforma web que combina:

- **Curso online** (aulas curtas em texto/markdown)
- **Quiz de fixação** com nota mínima para aprovação
- **Certificado de conclusão** com verificação pública
- **Registro blockchain** do hash do certificado (imutabilidade e autenticidade)
- **Tutor de IA** contextualizado ao conteúdo do curso

### Nome e significado

**Learn** (aprender) + **Proof** (prova / comprovação) = aprendizado com evidência verificável.

O diferencial não é “mais um curso online”, e sim **provar que o aluno absorveu o conteúdo** e permitir que terceiros **validem o certificado** sem depender exclusivamente da palavra da instituição ou do emissor.

---

## 2. Contexto e motivação

### Cenário profissional

Trabalho em uma empresa de **educação superior**. A ideia nasceu da combinação de:

1. **Proatividade da equipe** — cada membro domina temas práticos do dia a dia que raramente viram conteúdo formal.
2. **Valor para alunos** — dúvidas operacionais e pedagógicas recorrentes podem ser respondidas em microcursos curtos.
3. **Projeto de portfólio** — iniciativa que já acontece organicamente, estruturada em produto real com usuários.
4. **Tendências do setor** — microcredenciais, IA pedagógica e credenciais verificáveis.

### O que este projeto **não** é

- Não substitui disciplinas formais ou o LMS institucional.
- Não emite diploma ou certificação oficial da IES (salvo validação futura pela gestão).
- Não é um projeto imposto top-down — é uma **iniciativa colaborativa** que pode ser oficializada depois, se fizer sentido.

### O que este projeto **é**

- Biblioteca de **microconhecimentos** produzidos pela equipe.
- Trilha de aprendizado com **quiz e certificado**.
- Demonstração prática de **inovação aplicada** em contexto educacional.
- Base técnica reutilizável para pilotos maiores (extensionista, EAD, cursos livres).

---

## 3. Proposta de valor

| Pilar | Descrição | Beneficiário |
|-------|-----------|--------------|
| **Microcurso** | Aula curta (15–30 min) sobre um tema específico | Aluno, equipe |
| **Quiz** | Avaliação objetiva com nota mínima (padrão: 70%) | Aluno, organizador |
| **Certificado** | Comprovante de conclusão com UUID único | Aluno |
| **Verificação pública** | Página onde qualquer pessoa valida autenticidade | Empregador, parceiro, RH |
| **Blockchain** | Hash SHA-256 do certificado ancorado on-chain | Auditoria, confiança |
| **Tutor de IA** | Assistente que responde dúvidas sobre o conteúdo do curso | Aluno |

### Por que importa no ensino superior

- Certificados de cursos livres/extensionistas são difíceis de validar externamente.
- Suporte humano não escala para todas as dúvidas recorrentes.
- A instituição pode **praticar o que prega**: aprender, avaliar e certificar.
- Demonstra **empregabilidade e credibilidade** — competências verificáveis.

---

## 4. Iniciativa da equipe — “Cada um ensina”

### Conceito

Cada membro da equipe:

1. **Escolhe um tema** que domina (operacional, pedagógico, ferramental).
2. **Produz conteúdo** curto (texto, áudio ou vídeo — ideal: 15–30 minutos).
3. **Revisa o quiz** gerado pela IA (5–10 minutos).
4. **Publica** na plataforma LearnProof.

Os **alunos** (e colegas) acessam gratuitamente, fazem o quiz e recebem certificado de conclusão do microcurso.

### Princípios

| Princípio | Detalhe |
|-----------|---------|
| **Baixo atrito** | Quem ensina não precisa montar quiz — a IA gera rascunho; o autor revisa |
| **Conteúdo autêntico** | Voz de quem vive o problema no dia a dia |
| **Curto e aplicável** | Microcurso, não palestra de 2 horas |
| **Colaborativo** | A equipe constrói biblioteca permanente de conhecimento |
| **Aberto a alunos** | Material complementar, acessível via link |

### Formato sugerido por microcurso

```
Título: ___________________________________________
Autor: ____________________________________________
Para quem é: (calouros / veteranos / EAD / serviços / etc.)
Objetivo (1 frase): ________________________________
Conteúdo — 3 pontos principais:
  1.
  2.
  3.
Exemplo real do dia a dia:
Material entregue: ( ) texto  ( ) áudio  ( ) vídeo
Duração estimada: ______ minutos
```

### Exemplos de temas

- Como usar o portal do aluno
- Prazos acadêmicos que todo calouro confunde
- Documentação para estágio e TCC — o essencial
- Atendimento ao aluno: o que levar e o que evitar
- Organização de estudos para quem trabalha e estuda
- Ferramentas internas que a equipe usa no dia a dia
- LGPD na prática do atendimento

### Papéis

| Papel | Responsabilidade |
|-------|------------------|
| **Autor (colega)** | Escolhe tema, produz conteúdo, revisa quiz |
| **Organizador (você)** | Cadastra na plataforma, publica, divulga, mede métricas |
| **Aluno** | Consome aula, faz quiz, obtém certificado |
| **IA** | Gera rascunho de quiz, atua como tutor, explica erros (futuro) |
| **Gestão (futuro)** | Pode oficializar canal, divulgar institucionalmente |

### Cronograma piloto sugerido (30 dias)

| Semana | Atividade |
|--------|-----------|
| **1** | Convite à equipe; 3 voluntários escolhem tema; template preenchido |
| **2** | Entrega de conteúdo; cadastro na plataforma; geração de quiz |
| **3** | Revisão dos quizzes; publicação; divulgação inicial para alunos |
| **4** | Coleta de métricas; feedback; decisão de escalar ou ajustar |

---

## 5. Público-alvo

### Primário

- **Alunos** da instituição (calouros, veteranos, EAD) buscando respostas práticas e rápidas.

### Secundário

- **Membros da equipe** aprendendo uns com os outros (aprendizado cruzado).
- **Gestores** que queiram medir engajamento e identificar gaps de comunicação.
- **Parceiros / RH** que precisem validar certificados de microcursos (futuro).

---

## 6. Fluxos principais

### 6.1 Fluxo do aluno

```
Registrar / Login
    ↓
Explorar catálogo de microcursos
    ↓
Matricular-se no curso
    ↓
Ler/assistir aulas (marcar como concluídas)
    ↓
Usar tutor de IA (opcional, durante o curso)
    ↓
Fazer quiz final
    ↓
    ├── Reprovado → tentar novamente
    └── Aprovado → certificado emitido
                        ↓
                  Hash registrado (blockchain)
                        ↓
                  Link público de verificação
```

### 6.2 Fluxo do autor (equipe)

```
Escolher tema
    ↓
Preencher template
    ↓
Produzir conteúdo (15–30 min)
    ↓
Enviar ao organizador
    ↓
Organizador cadastra na plataforma
    ↓
IA gera rascunho do quiz
    ↓
Autor revisa e aprova questões
    ↓
Publicação + divulgação
```

### 6.3 Fluxo de verificação do certificado

```
Terceiro acessa link público (/certificados/{uuid}/verificar)
    ↓
Visualiza: aluno, curso, data, hash, transação blockchain
    ↓
Sistema confirma integridade do hash
    ↓
Status: autêntico ✓ ou inválido ✗
```

---

## 7. Funcionalidades da plataforma

### Implementadas (MVP)

| Funcionalidade | Descrição |
|----------------|-----------|
| Autenticação | Registro, login, verificação de e-mail (Laravel Breeze) |
| Catálogo de cursos | Listagem de microcursos publicados |
| Página do curso | Descrição, aulas, progresso, matrícula |
| Aulas | Conteúdo em Markdown renderizado |
| Progresso | Marcação de aulas concluídas; barra de progresso |
| Quiz | Questões de múltipla escolha; nota e aprovação |
| Certificado | Emissão automática ao passar no quiz |
| Verificação pública | Página aberta sem login |
| Tutor de IA | Chat contextualizado por curso (OpenAI ou modo demo) |
| Blockchain (mock) | Hash ancorado em modo simulado para desenvolvimento |
| Dashboard | Matrículas e certificados do usuário |

### Planejadas (roadmap)

| Funcionalidade | Fase |
|----------------|------|
| Geração automática de quiz pela IA a partir do conteúdo | 2 |
| Painel admin para autores cadastrarem cursos | 2 |
| Upload de vídeo/áudio | 2 |
| Blockchain real (Polygon Amoy / Base Sepolia) | 2 |
| Exportação PDF do certificado | 3 |
| Feedback da IA pós-quiz (“você errou porque…”) | 3 |
| Trilhas / categorias (calouro, EAD, serviços) | 3 |
| Gamificação (badges, ranking de conclusão) | 4 |
| Integração com canal oficial da instituição | 4 |

---

## 8. Arquitetura técnica

### Stack

| Camada | Tecnologia |
|--------|------------|
| Backend | PHP 8.3, Laravel 13 |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Banco de dados | SQLite (dev) / MySQL ou PostgreSQL (prod) |
| Autenticação | Laravel Breeze |
| Fila | Database queue (Laravel) |
| IA | OpenAI API (gpt-4o-mini) |
| Blockchain | Mock (dev) → EVM testnet (prod) |

### Diagrama de arquitetura

```
┌─────────────────────────────────────────────────────────┐
│                    Navegador (Aluno)                     │
└─────────────────────────┬───────────────────────────────┘
                          │
┌─────────────────────────▼───────────────────────────────┐
│              Laravel 13 — LearnProof                     │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐ │
│  │ Controllers │  │   Models    │  │    Services     │ │
│  │ Course      │  │ Course      │  │ AiTutorService  │ │
│  │ Lesson      │  │ Lesson      │  │ QuizGrader      │ │
│  │ Quiz        │  │ Quiz        │  │ Certificate     │ │
│  │ Certificate │  │ Certificate │  │ Blockchain      │ │
│  │ AiTutor     │  │ Enrollment  │  │                 │ │
│  └─────────────┘  └─────────────┘  └─────────────────┘ │
└──────────┬──────────────────────┬─────────────────────┘
           │                      │
    ┌──────▼──────┐        ┌──────▼──────┐
    │  SQLite /   │        │  OpenAI API │
    │  MySQL      │        │  (tutor IA) │
    └─────────────┘        └─────────────┘
                                  │
                           ┌──────▼──────┐
                           │  Blockchain │
                           │  (EVM mock/ │
                           │   testnet)  │
                           └─────────────┘
```

### Estrutura de pastas relevante

```
app/
├── Http/Controllers/
│   ├── CourseController.php
│   ├── LessonController.php
│   ├── QuizController.php
│   ├── CertificateController.php
│   └── AiTutorController.php
├── Models/
│   ├── Course.php, Lesson.php, Enrollment.php
│   ├── Quiz.php, Question.php, QuestionOption.php
│   ├── QuizAttempt.php, QuizAnswer.php
│   ├── Certificate.php, AiChatMessage.php
│   └── User.php
└── Services/
    ├── AiTutorService.php
    ├── QuizGraderService.php
    ├── CertificateIssuerService.php
    └── BlockchainAnchorService.php

config/learnproof.php
database/migrations/2026_05_29_000001_create_learnproof_tables.php
database/seeders/LearnProofSeeder.php
resources/views/courses/, lessons/, quizzes/, certificates/
routes/web.php
```

### Rotas principais

| Método | Rota | Nome | Descrição |
|--------|------|------|-----------|
| GET | `/cursos` | courses.index | Catálogo |
| GET | `/cursos/{slug}` | courses.show | Detalhe do curso |
| POST | `/cursos/{slug}/matricular` | courses.enroll | Matrícula |
| GET | `/cursos/{slug}/aulas/{slug}` | lessons.show | Aula |
| POST | `/cursos/{slug}/aulas/{slug}/concluir` | lessons.complete | Concluir aula |
| GET | `/cursos/{slug}/quiz` | quizzes.show | Quiz |
| POST | `/cursos/{slug}/quiz` | quizzes.submit | Enviar respostas |
| GET | `/certificados/{uuid}` | certificates.show | Certificado (auth) |
| GET | `/certificados/{uuid}/verificar` | certificates.verify | Verificação pública |
| POST | `/cursos/{slug}/ia/chat` | ai.chat | Tutor de IA |

---

## 9. Modelo de dados

### Entidades

```
User
 ├── Enrollment (matrícula em curso)
 │    └── LessonProgress (aula concluída)
 ├── QuizAttempt (tentativa de quiz)
 │    └── QuizAnswer (resposta)
 ├── Certificate (certificado emitido)
 └── AiChatMessage (histórico tutor IA)

Course (microcurso)
 ├── Lesson (aula)
 ├── Quiz (avaliação final)
 │    └── Question (questão)
 │         └── QuestionOption (alternativa)
 └── Certificate
```

### Tabela `certificates` (campos-chave)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| uuid | UUID | Identificador público único |
| content_hash | SHA-256 | Hash do payload do certificado |
| blockchain_tx_hash | string | Hash da transação on-chain |
| blockchain_network | string | Rede (ex.: mock, polygon-amoy) |
| metadata | JSON | Nome do aluno, curso, nota, emissor |
| issued_at | timestamp | Data de emissão |

### Regras de negócio

- Nota mínima padrão: **70%** (configurável por curso e quiz).
- Certificado emitido **uma vez por usuário/curso** (unique constraint).
- Quiz só liberado após **100% das aulas concluídas**.
- Matrícula obrigatória para acessar aulas, quiz e tutor.
- Verificação pública **não exige login**.

---

## 10. Integrações — IA e Blockchain

### 10.1 Tutor de IA

**Serviço:** `AiTutorService`

**Comportamento:**
- Recebe mensagem do aluno no contexto do curso matriculado.
- Monta prompt de sistema com título do curso e nomes das aulas.
- Envia histórico recente à OpenAI API.
- Persiste mensagens em `ai_chat_messages`.
- **Modo demo:** resposta estática quando `OPENAI_API_KEY` não está configurada.

**Configuração (.env):**

```env
AI_ENABLED=true
OPENAI_API_KEY=sua-chave-aqui
OPENAI_MODEL=gpt-4o-mini
AI_MAX_HISTORY=20
```

**Uso pedagógico responsável:**
- Tutor limitado ao conteúdo do curso (não responde assuntos externos).
- Não emite certificados nem altera notas.
- Incentiva revisão das aulas e quiz.

### 10.2 Geração de quiz pela IA (planejado — Fase 2)

Fluxo futuro:
1. Autor envia texto da aula.
2. IA gera 5–8 questões de múltipla escolha + explicações.
3. Autor revisa e aprova.
4. Questões publicadas no quiz do curso.

### 10.3 Blockchain

**Serviço:** `BlockchainAnchorService`

**Modo atual (mock):**
- Gera hash SHA-256 do certificado.
- Simula `blockchain_tx_hash` para desenvolvimento e demonstração.
- Verificação compara integridade do hash localmente.

**Modo futuro (testnet/mainnet):**
- Contrato Solidity: `anchorCertificate(bytes32 hash)`
- Rede sugerida: **Polygon Amoy** (testnet) ou **Base Sepolia**
- Job assíncrono Laravel registra hash após emissão
- Página de verificação linka para block explorer

**Configuração (.env):**

```env
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_MODE=mock
BLOCKCHAIN_NETWORK=polygon-amoy
BLOCKCHAIN_RPC_URL=
BLOCKCHAIN_CONTRACT_ADDRESS=
BLOCKCHAIN_WALLET_PRIVATE_KEY=
```

**Por que blockchain aqui?**
- Não substitui registro acadêmico oficial.
- Garante **integridade** e **timestamp** imutável do certificado.
- Permite verificação por terceiros sem acesso ao banco da plataforma.

---

## 11. O que já está implementado

### MVP funcional

- [x] Migration completa do modelo de dados
- [x] Models Eloquent com relacionamentos
- [x] Controllers: Course, Lesson, Quiz, Certificate, AiTutor
- [x] Services: IA, quiz, certificado, blockchain mock
- [x] Views: catálogo, curso, aula, quiz, certificado, verificação
- [x] Dashboard com matrículas e certificados
- [x] Seeder com curso demo: *Fundamentos de IA Generativa* (3 aulas, 4 questões)
- [x] Usuário demo: `aluno@learnproof.test`
- [x] Config centralizada: `config/learnproof.php`
- [x] Variáveis de ambiente documentadas em `.env.example`

### Curso demo incluído

| Aula | Tema |
|------|------|
| 1 | O que é IA Generativa? |
| 2 | Engenharia de Prompts |
| 3 | Ética, Privacidade e Blockchain |

Quiz: 4 questões, nota mínima 70%.

---

## 12. Roadmap de evolução

### Fase 1 — MVP interno ✅ (atual)

- Plataforma base funcionando
- 1 curso demo
- Tutor IA (demo + OpenAI)
- Certificado + verificação + blockchain mock
- Iniciativa “cada um ensina” com 3–5 colegas voluntários

### Fase 2 — Produto colaborativo (30–60 dias)

- [ ] Painel para autores submeterem conteúdo
- [ ] Geração de quiz pela IA com revisão humana
- [ ] 10+ microcursos da equipe publicados
- [ ] Divulgação para alunos (WhatsApp, mural, portal)
- [ ] Blockchain testnet real (Polygon Amoy)

### Fase 3 — Escala e qualidade (60–120 dias)

- [ ] Categorias e trilhas (calouro, EAD, serviços)
- [ ] PDF do certificado
- [ ] Feedback pós-quiz pela IA
- [ ] Métricas e dashboard para organizador
- [ ] Revisão periódica de conteúdo (6 meses)

### Fase 4 — Institucionalização (se aprovado pela gestão)

- [ ] Marca oficial da instituição
- [ ] Canal de divulgação institucional
- [ ] Integração com área de extensionista ou EAD
- [ ] Microcredenciais reconhecidas internamente
- [ ] Possível expansão para parceiros empregadores

---

## 13. Métricas e KPIs

### Piloto (30 dias)

| Métrica | Meta sugerida |
|---------|---------------|
| Membros da equipe que publicaram | 100% dos voluntários |
| Microcursos publicados | ≥ 3 |
| Alunos matriculados | ≥ 50 |
| Taxa de conclusão (aulas + quiz) | ≥ 60% |
| Nota média no quiz | ≥ 75% |
| Uso do tutor de IA | ≥ 30% dos matriculados |
| Verificações de certificado | registrar total |

### Indicadores de sucesso qualitativo

- Feedback positivo de alunos (“aprendi algo aplicável”)
- Colegas pedindo para participar espontaneamente
- Gestor compartilhando ou elogiando iniciativa
- Redução de dúvidas repetitivas no atendimento (futuro)

---

## 14. Aspectos legais e institucionais

### Linguagem recomendada na plataforma

> *“Iniciativa colaborativa de compartilhamento de conhecimento. Conteúdo complementar, de caráter informativo, não substitui disciplinas formais nem emite certificação oficial da instituição.”*

### Cuidados

| Tema | Orientação |
|------|------------|
| **LGPD** | Não usar nomes ou dados de alunos reais em exemplos |
| **Certificado** | “Certificado de conclusão do microcurso LearnProof” |
| **Marca** | Usar logo institucional somente com autorização |
| **Conteúdo** | Autor responsável pela revisão factual |
| **IA** | Quiz revisado por humano; tutor não substitui orientação oficial |
| **Avaliação** | Nota do quiz **não** vinculada a RH ou desempenho da equipe |

### Evolução para oficialização

Quando houver tração (alunos usando, equipe engajada), apresentar à gestão:

> “Temos X microcursos, Y alunos concluíram, Z certificados verificados. Proponho oficializar como canal complementar de apoio ao aluno.”

---

## 15. Como apresentar à gestão

### Pitch (30 segundos)

> “Estruturei uma biblioteca de microcursos produzidos pela nossa equipe: cada pessoa ensina um tema prático em 20 minutos, a IA gera quiz de fixação, e o aluno recebe certificado verificável. Já temos [X] temas e [Y] conclusões. É complementar à grade, barato de manter, e posiciona a equipe como referência de conhecimento aplicado.”

### O que levar na reunião

1. Demo ao vivo (matrícula → aula → quiz → certificado → verificação)
2. Print do link público de verificação
3. Tabela de métricas do piloto
4. Depoimento de 1–2 alunos (se houver)
5. Proposta de próximo passo (oficializar ou expandir)

### O que **não** dizer

- “Vamos substituir o LMS”
- “Blockchain vai revolucionar a educação”
- “Preciso de budget grande”
- “Todo mundo precisa participar ou será avaliado”

### O que **sim** enfatizar

- Proatividade documentada da equipe
- Valor real para alunos
- Baixo custo, alto impacto percebido
- Alinhamento com tendências (microcredenciais, IA responsável)
- Projeto que **já está acontecendo**, não só ideia

---

## 16. Guia operacional para a equipe

### Para quem quer ensinar

1. Escolha um tema que você explica bem no dia a dia.
2. Preencha o template (seção 4).
3. Produza o conteúdo — pode ser:
   - Texto escrito (Word/Google Docs → Markdown)
   - Vídeo curto gravado no celular (link YouTube/Drive)
   - Áudio + bullet points
4. Envie ao organizador do projeto.
5. Quando receber o rascunho do quiz, revise em ~10 minutos.
6. Pronto — seu microcurso estará disponível para alunos.

### Para o organizador

1. Receber conteúdo do autor.
2. Cadastrar curso + aulas na plataforma (hoje via seeder/admin; futuro via painel).
3. Acionar geração de quiz (hoje manual; futuro via IA).
4. Enviar quiz ao autor para revisão.
5. Publicar (`is_published = true`).
6. Divulgar link do curso.
7. Acompanhar métricas no dashboard.

### Mensagem pronta para convidar a equipe

> Pessoal, estou montando uma biblioteca aberta de microconteúdos na plataforma LearnProof: cada um escolhe um tema que domina, grava ou escreve em ~20 min, e eu publico com um quiz no final. A ideia é disponibilizar para alunos como material complementar. Quem topa ser voluntário nos primeiros 3? Eu cuido de publicar e montar o quiz.

---

## 17. Como executar localmente

### Pré-requisitos

- PHP 8.3+
- Composer
- Node.js 18+
- SQLite (já configurado) ou MySQL

### Instalação

```powershell
cd c:\Users\Julie\projetos\curso-ia
composer install
cp .env.example .env   # se ainda não existir
php artisan key:generate
php artisan migrate:fresh --seed
npm install
npm run build
```

### Executar em desenvolvimento

```powershell
composer run dev
```

Abre simultaneamente: servidor PHP, fila, logs e Vite.

### Acessos

| Item | Valor |
|------|-------|
| URL | http://localhost:8000 |
| Usuário demo | aluno@learnproof.test |
| Senha | password (padrão Laravel factory) |
| Curso demo | /cursos/fundamentos-ia-generativa |

### Configurar IA (opcional)

No `.env`:

```env
OPENAI_API_KEY=sk-...
```

Sem a chave, o tutor funciona em **modo demonstração**.

---

## 18. Anexos

### A. Glossário

| Termo | Definição |
|-------|-----------|
| **Microcurso** | Curso curto (1 tema, 1–3 aulas, ≤ 1 h total) |
| **Microcredencial** | Certificado de habilidade/conhecimento específico |
| **Hash SHA-256** | Impressão digital criptográfica do conteúdo |
| **Ancoragem blockchain** | Registro imutável do hash em rede distribuída |
| **Tutor de IA** | Assistente conversacional limitado ao conteúdo do curso |
| **Verificação pública** | Consulta de autenticidade sem login |

### B. Configuração completa (.env)

```env
APP_NAME=LearnProof
LEARNPROOF_NAME=LearnProof

AI_ENABLED=true
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
AI_MAX_HISTORY=20

BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_MODE=mock
BLOCKCHAIN_NETWORK=polygon-amoy
BLOCKCHAIN_RPC_URL=
BLOCKCHAIN_CONTRACT_ADDRESS=
BLOCKCHAIN_WALLET_PRIVATE_KEY=

CERTIFICATE_MIN_SCORE=70
```

### C. Frases para portfólio / LinkedIn

> Estruturei o LearnProof, plataforma de microcursos com quiz, certificado verificável via blockchain e tutor de IA. Lidero iniciativa colaborativa em que a equipe produz conteúdos práticos disponibilizados a alunos, com métricas de conclusão e verificação pública de credenciais.

### D. Referências e inspiração

- Microcredenciais e Open Badges (IMS Global)
- Registros verificáveis (W3C Verifiable Credentials)
- IA pedagógica responsável (UNESCO Guidance on AI in Education)
- Laravel 13 + Breeze (base técnica)

---

## Controle de versão deste documento

| Versão | Data | Alteração |
|--------|------|-----------|
| 1.0 | maio/2026 | Documento inicial — MVP + iniciativa equipe |

---

*LearnProof — Aprender com prova.*
