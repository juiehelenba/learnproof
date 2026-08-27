<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class LearnProofSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::query()->updateOrCreate(
            ['slug' => 'fundamentos-ia-generativa'],
            [
                'title' => 'Fundamentos de IA Generativa',
                'description' => 'Curso introdutório para entender como funcionam modelos como ChatGPT, Claude e Gemini: o que é IA generativa, como escrever bons prompts, quais cuidados éticos adotar e como certificados digitais podem ser verificados com blockchain. Ideal para quem nunca usou IA ou quer usar de forma mais consciente no trabalho e nos estudos.',
                'thumbnail' => null,
                'passing_score' => 70,
                'is_published' => true,
            ]
        );

        $lessons = [
            [
                'title' => 'O que é IA Generativa?',
                'slug' => 'o-que-e-ia-generativa',
                'content' => <<<'MD'
## Bem-vindo à Aula 1

Nesta primeira aula você vai entender **o que é IA generativa**, como ela difere de outras formas de inteligência artificial e por que ferramentas como ChatGPT, Gemini e Claude se tornaram tão populares nos últimos anos.

### Objetivos de aprendizagem

Ao final desta aula, você será capaz de:

- Explicar, com suas palavras, o que é um modelo generativo
- Diferenciar IA generativa de IA discriminativa (classificadora)
- Identificar casos de uso reais no dia a dia
- Reconhecer o risco de "alucinações" nas respostas

---

### O que é Inteligência Artificial Generativa?

**IA generativa** é um tipo de sistema que **cria conteúdo novo** — texto, imagens, áudio, vídeo ou código — a partir de padrões aprendidos em enormes volumes de dados.

Diferente de um sistema que apenas **classifica** (ex.: "esta foto é de um gato" ou "este e-mail é spam"), o modelo generativo **produz** algo que não existia antes: um parágrafo, um resumo, uma imagem, uma função em Python.

**Exemplo prático:** você pede *"Explique o que é fotossíntese para um aluno do ensino médio"* e o modelo **gera** uma explicação original, em linguagem adequada.

### Como funciona, em linguagem simples?

1. O modelo é treinado com bilhões de exemplos de texto (livros, sites, artigos).
2. Ele aprende padrões estatísticos: quais palavras costumam vir depois de quais.
3. Quando você envia um **prompt** (pergunta ou instrução), o modelo **prevê** a sequência de tokens (pedaços de palavras) mais provável como resposta.
4. O resultado parece uma conversa ou um texto escrito por um humano — mas é uma **previsão estatística**, não "pensamento" no sentido humano.

### Conceitos essenciais

| Termo | Significado |
|-------|-------------|
| **LLM** (Large Language Model) | Modelo de linguagem de grande escala, treinado para trabalhar com texto |
| **Prompt** | A instrução ou pergunta que você envia ao modelo |
| **Token** | Unidade mínima de texto processada pelo modelo (pode ser palavra ou parte dela) |
| **Alucinação** | Quando o modelo responde com confiança algo **factualmente incorreto** |
| **Contexto** | Informação adicional que você fornece para orientar a resposta |

### Onde a IA generativa já é usada?

- **Educação:** resumos, exercícios, tutoria personalizada
- **Atendimento:** chatbots que respondem dúvidas frequentes
- **Produtividade:** rascunhos de e-mails, relatórios, planilhas
- **Programação:** geração e explicação de código
- **Marketing:** ideias de campanhas, variações de textos

### Limitações importantes

> **Atenção:** modelos generativos não "sabem" a verdade — eles **geram texto plausível**. Sempre valide informações críticas (datas, leis, dados médicos, valores numéricos) em fontes confiáveis.

- Podem inventar referências bibliográficas
- Refletem vieses presentes nos dados de treinamento
- Não substituem julgamento humano em decisões sensíveis
- Podem desatualizar-se (conhecimento limitado à data de corte do treinamento)

### Para refletir

Antes de avançar para a próxima aula, pense: **em qual tarefa do seu dia a dia a IA generativa poderia ajudar — e onde ela definitivamente não deveria decidir sozinha?**

---

**Próximo passo:** Aula 2 — Engenharia de Prompts: como obter respostas melhores e mais úteis.
MD,
                'duration_minutes' => 20,
                'sort_order' => 1,
            ],
            [
                'title' => 'Engenharia de Prompts',
                'slug' => 'engenharia-de-prompts',
                'content' => <<<'MD'
## Bem-vindo à Aula 2

Escrever um **prompt** eficaz é a habilidade mais importante para quem usa IA generativa. Nesta aula você aprenderá técnicas práticas para obter respostas mais claras, precisas e úteis — seja no trabalho, nos estudos ou em projetos pessoais.

### Objetivos de aprendizagem

- Aplicar a estrutura **contexto → tarefa → formato → restrições**
- Usar exemplos (few-shot) para guiar o modelo
- Evitar prompts vagos que geram respostas genéricas
- Iterar e refinar prompts com base nos resultados

---

### O que é Engenharia de Prompts?

**Prompt engineering** é a prática de formular instruções para modelos de IA de forma estruturada, aumentando a qualidade e a previsibilidade das respostas.

Não é "engenharia" no sentido de programar — é **comunicação estratégica** com o modelo.

### Anatomia de um bom prompt

Um prompt eficaz geralmente contém quatro elementos:

1. **Papel (role):** quem o modelo deve "ser"
2. **Contexto:** informações de fundo relevantes
3. **Tarefa:** o que você quer que seja feito
4. **Formato:** como a resposta deve ser entregue

**Exemplo fraco:**
```
Fale sobre marketing.
```

**Exemplo forte:**
```
Você é um professor de marketing digital para iniciantes.

Contexto: estou criando um post para Instagram de uma faculdade EAD.

Tarefa: escreva 3 legendas curtas (máx. 150 caracteres) destacando
flexibilidade de horários para quem trabalha.

Formato: lista numerada, tom acolhedor, sem emojis excessivos.
```

### Técnicas que funcionam

#### 1. Seja específico
Quanto mais claro o objetivo, melhor a resposta. Inclua público-alvo, tom, extensão e propósito.

#### 2. Defina o formato de saída
Peça listas, tabelas, JSON, parágrafos numerados ou passo a passo — isso evita textos longos e desorganizados.

#### 3. Use exemplos (few-shot)
Mostre 1–2 exemplos do resultado desejado. O modelo tende a imitar o padrão.

#### 4. Peça raciocínio passo a passo
Para problemas complexos, adicione: *"Explique seu raciocínio passo a passo antes de dar a resposta final."*

#### 5. Itere
A primeira resposta raramente é perfeita. Refine: *"Torne mais conciso"*, *"Adicione um exemplo prático"*, *"Reescreva para um público leigo"*.

### Erros comuns

| Erro | Por que atrapalha |
|------|-------------------|
| Prompt de uma palavra | Resposta genérica e inútil |
| Não definir público-alvo | Tom inadequado |
| Pedir tudo de uma vez | Resposta superficial |
| Confiar cegamente | Alucinações passam despercebidas |
| Não revisar a saída | Erros factuais chegam ao usuário final |

### Template para copiar

```
Você é [PAPEL].

Contexto: [SITUAÇÃO / PÚBLICO / OBJETIVO]

Tarefa: [O QUE DEVE SER FEITO]

Formato: [COMO ENTREGAR — lista, tabela, parágrafos, etc.]

Restrições: [O QUE EVITAR — jargões, tamanho, tom, etc.]
```

### Exercício mental

Reescreva o prompt fraco abaixo usando a anatomia completa:

> "Me ajuda com um texto sobre matrícula."

*(Pense: quem é o público? Qual canal? Qual tom? Qual tamanho?)*

---

**Próximo passo:** Aula 3 — Ética, Privacidade e verificação de certificados com blockchain.
MD,
                'duration_minutes' => 25,
                'sort_order' => 2,
            ],
            [
                'title' => 'Ética, Privacidade e Blockchain',
                'slug' => 'etica-privacidade-blockchain',
                'content' => <<<'MD'
## Bem-vindo à Aula 3

Usar IA generativa traz benefícios reais, mas também **responsabilidades**. Nesta aula final você vai aprender boas práticas de ética e privacidade — e entender como a **blockchain** pode tornar certificados digitais **verificáveis** sem expor dados desnecessários.

### Objetivos de aprendizagem

- Identificar dados que **nunca** devem ser enviados a modelos públicos
- Aplicar princípios de uso responsável de IA em contexto educacional
- Explicar o que é um hash e por que ele é usado em certificados
- Descrever como funciona a verificação pública de um certificado LearnProof

---

### Ética no uso de IA generativa

#### Transparência
Se você usou IA para produzir ou revisar conteúdo, **documente isso** quando relevante — especialmente em trabalhos acadêmicos, materiais institucionais ou comunicações oficiais.

#### Responsabilidade humana
A IA **assiste**; a **decisão final** é sempre humana. Não publique, envie ou compartilhe respostas sem revisar.

#### Vieses
Modelos podem reproduzir estereótipos presentes nos dados de treinamento. Revise conteúdo sensível a diversidade, inclusão e equidade.

#### Acessibilidade
Conteúdo gerado por IA deve ser adaptado ao público — linguagem clara, exemplos inclusivos, formatos acessíveis.

### Privacidade e LGPD

**Nunca envie a modelos públicos:**

- CPF, RG, endereço completo
- Dados de saúde
- Senhas, tokens ou credenciais
- Informações bancárias
- Dados pessoais de terceiros sem base legal
- Conteúdo confidencial da instituição

> **Regra prática:** se você não colocaria a informação em um e-mail para um desconhecido, **não coloque no prompt**.

Em ambientes corporativos ou educacionais, prefira ferramentas com **política de privacidade clara** e, quando possível, contratos que garantam que seus dados **não serão usados para treinar** modelos públicos.

### Certificados digitais verificáveis

Quando você conclui este curso e passa no quiz, a plataforma LearnProof emite um **certificado de conclusão** com:

- Identificador único (UUID)
- Nome do aluno e do curso
- Nota obtida no quiz
- Data de emissão
- **Hash SHA-256** do conteúdo do certificado

### O que é um hash?

Um **hash** é uma "impressão digital" matemática de um conjunto de dados. Se **qualquer informação** do certificado for alterada, o hash muda completamente — o que torna fraudes detectáveis.

```
Certificado original  →  SHA-256  →  a3f8b2c1d9e0... (64 caracteres)
Certificado alterado  →  SHA-256  →  7k2m9x4p1q8r... (totalmente diferente)
```

### Blockchain: para que serve aqui?

A **blockchain** registra o hash do certificado de forma **imutável** e **datada**. Isso permite que qualquer pessoa:

1. Acesse o link público de verificação
2. Confira se o certificado é autêntico
3. Veja a transação registrada (em produção, em rede como Polygon)

**Importante:** a blockchain **não armazena** seu documento completo nem dados sensíveis — apenas o hash, suficiente para provar integridade.

### Fluxo no LearnProof

```
Aluno conclui aulas → Passa no quiz (≥ 70%)
        ↓
Certificado emitido com UUID + hash SHA-256
        ↓
Hash ancorado na blockchain (registro imutável)
        ↓
Link público: /certificados/{uuid}/verificar
        ↓
Empregador ou terceiro confirma autenticidade ✓
```

### Checklist de uso responsável

- [ ] Revisei o conteúdo gerado por IA antes de usar?
- [ ] Evitei dados pessoais nos prompts?
- [ ] Informei quando IA foi utilizada (se aplicável)?
- [ ] Validei fatos críticos em fontes confiáveis?
- [ ] Entendo que certificado LearnProof é de **conclusão do microcurso**, não diploma oficial?

---

**Parabéns por concluir as aulas!** Agora faça o **Quiz Final** para consolidar o aprendizado e receber seu certificado verificável.
MD,
                'duration_minutes' => 20,
                'sort_order' => 3,
            ],
        ];

        foreach ($lessons as $data) {
            $course->lessons()->updateOrCreate(
                ['course_id' => $course->id, 'slug' => $data['slug']],
                $data
            );
        }

        $quiz = Quiz::query()->updateOrCreate(
            ['course_id' => $course->id],
            [
                'title' => 'Avaliação Final — Fundamentos de IA Generativa',
                'passing_score' => 70,
            ]
        );

        $quiz->questions()->each(fn (Question $q) => $q->options()->delete());
        $quiz->questions()->delete();

        $questions = [
            [
                'text' => 'O que diferencia a IA generativa de um sistema que apenas classifica dados (como "spam" ou "não spam")?',
                'explanation' => 'IA generativa produz conteúdo novo (texto, imagem, código). Sistemas discriminativos apenas categorizam entradas existentes.',
                'options' => [
                    ['text' => 'A generativa apenas armazena dados sem processá-los', 'is_correct' => false],
                    ['text' => 'A generativa cria conteúdo novo com base em padrões aprendidos', 'is_correct' => true],
                    ['text' => 'A generativa só funciona com imagens, nunca com texto', 'is_correct' => false],
                    ['text' => 'Não há diferença — são o mesmo tipo de sistema', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'O que significa "alucinação" em um modelo de linguagem (LLM)?',
                'explanation' => 'Alucinação ocorre quando o modelo gera informação incorreta ou inventada, mas apresentada com tom confiante e plausível.',
                'options' => [
                    ['text' => 'Erro de conexão com a internet durante a geração', 'is_correct' => false],
                    ['text' => 'Resposta factualmente incorreta apresentada de forma convincente', 'is_correct' => true],
                    ['text' => 'Modo de economia de energia do servidor', 'is_correct' => false],
                    ['text' => 'Quando o usuário envia um prompt muito longo', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Qual elemento NÃO faz parte da anatomia de um prompt eficaz descrita neste curso?',
                'explanation' => 'Um bom prompt inclui papel, contexto, tarefa e formato. A cor de fundo da interface não influencia a resposta do modelo.',
                'options' => [
                    ['text' => 'Definição do papel que o modelo deve assumir', 'is_correct' => false],
                    ['text' => 'Contexto e informações de fundo relevantes', 'is_correct' => false],
                    ['text' => 'A cor de fundo preferida do usuário na interface', 'is_correct' => true],
                    ['text' => 'Formato esperado da resposta (lista, tabela, parágrafos)', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Por que é importante definir o formato de saída no prompt?',
                'explanation' => 'Definir o formato (lista, tabela, JSON etc.) orienta o modelo a entregar a resposta de forma organizada e útil para o seu caso de uso.',
                'options' => [
                    ['text' => 'Porque o modelo só funciona com prompts em JSON', 'is_correct' => false],
                    ['text' => 'Para obter respostas mais organizadas e adequadas ao uso pretendido', 'is_correct' => true],
                    ['text' => 'Para aumentar o consumo de tokens e prolongar a resposta', 'is_correct' => false],
                    ['text' => 'Porque prompts sem formato são bloqueados pela plataforma', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'De acordo com as boas práticas de privacidade, qual dado NÃO deve ser enviado a modelos de IA públicos?',
                'explanation' => 'CPF, dados de saúde, senhas e informações bancárias são dados sensíveis protegidos pela LGPD e nunca devem ser incluídos em prompts.',
                'options' => [
                    ['text' => 'Um título genérico de artigo acadêmico', 'is_correct' => false],
                    ['text' => 'CPF ou dados pessoais identificáveis de alunos', 'is_correct' => true],
                    ['text' => 'Uma pergunta sobre conceitos de matemática', 'is_correct' => false],
                    ['text' => 'O pedido para resumir um texto público da internet', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Qual é a função do hash SHA-256 no certificado LearnProof?',
                'explanation' => 'O hash funciona como impressão digital do certificado. Qualquer alteração nos dados muda o hash, permitindo detectar fraudes.',
                'options' => [
                    ['text' => 'Armazenar o PDF completo do certificado na blockchain', 'is_correct' => false],
                    ['text' => 'Servir como impressão digital que detecta qualquer alteração nos dados', 'is_correct' => true],
                    ['text' => 'Substituir a necessidade de o aluno fazer o quiz', 'is_correct' => false],
                    ['text' => 'Criptografar a senha de acesso do aluno à plataforma', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $i => $qData) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'text' => $qData['text'],
                'explanation' => $qData['explanation'],
                'sort_order' => $i + 1,
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'text' => $opt['text'],
                    'is_correct' => $opt['is_correct'],
                ]);
            }
        }
    }
}
