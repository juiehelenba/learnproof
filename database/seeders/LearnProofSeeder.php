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
        $course = Course::create([
            'title' => 'Fundamentos de IA Generativa',
            'slug' => 'fundamentos-ia-generativa',
            'description' => 'Aprenda os conceitos essenciais de IA generativa, prompts e ética — com quiz adaptativo e certificado verificável na blockchain.',
            'thumbnail' => null,
            'passing_score' => 70,
            'is_published' => true,
        ]);

        $lessons = [
            [
                'title' => 'O que é IA Generativa?',
                'slug' => 'o-que-e-ia-generativa',
                'content' => "## Introdução\n\nIA generativa cria **texto, imagem, áudio e código** a partir de padrões aprendidos em grandes volumes de dados.\n\n### Conceitos-chave\n- **Modelo de linguagem (LLM)**: prevê a próxima palavra/token\n- **Prompt**: instrução que guia a resposta\n- **Alucinação**: resposta plausível porém incorreta\n\n> Dica: sempre valide fatos críticos com fontes confiáveis.",
                'duration_minutes' => 12,
                'sort_order' => 1,
            ],
            [
                'title' => 'Engenharia de Prompts',
                'slug' => 'engenharia-de-prompts',
                'content' => "## Boas práticas\n\n1. Seja **específico** no objetivo\n2. Defina **formato** da saída (lista, JSON, tabela)\n3. Use **exemplos** (few-shot)\n4. Peça **raciocínio passo a passo** quando necessário\n\n```\nVocê é um tutor. Explique X em 3 parágrafos para iniciantes.\n```",
                'duration_minutes' => 15,
                'sort_order' => 2,
            ],
            [
                'title' => 'Ética, Privacidade e Blockchain',
                'slug' => 'etica-privacidade-blockchain',
                'content' => "## Responsabilidade\n\n- Não envie dados sensíveis a modelos públicos\n- Documente o uso de IA em projetos educacionais\n\n## Certificados na blockchain\n\nUm **hash SHA-256** do certificado é ancorado on-chain, permitindo **verificação pública** sem expor dados pessoais desnecessários.",
                'duration_minutes' => 10,
                'sort_order' => 3,
            ],
        ];

        foreach ($lessons as $data) {
            $course->lessons()->create($data);
        }

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Avaliação Final — IA Generativa',
            'passing_score' => 70,
        ]);

        $questions = [
            [
                'text' => 'O que caracteriza um modelo de IA generativa?',
                'explanation' => 'Modelos generativos produzem conteúdo novo (texto, imagem, etc.) a partir de padrões aprendidos.',
                'options' => [
                    ['text' => 'Apenas classifica dados existentes', 'is_correct' => false],
                    ['text' => 'Gera conteúdo novo com base em padrões aprendidos', 'is_correct' => true],
                    ['text' => 'Armazena dados sem processamento', 'is_correct' => false],
                    ['text' => 'Substitui totalmente revisão humana', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Qual prática melhora a qualidade de um prompt?',
                'explanation' => 'Prompts específicos, com formato e contexto, tendem a gerar respostas mais úteis.',
                'options' => [
                    ['text' => 'Usar perguntas vagas de uma palavra', 'is_correct' => false],
                    ['text' => 'Definir objetivo, contexto e formato esperado', 'is_correct' => true],
                    ['text' => 'Nunca dar exemplos ao modelo', 'is_correct' => false],
                    ['text' => 'Ignorar o público-alvo da resposta', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Por que ancorar o hash do certificado na blockchain?',
                'explanation' => 'O registro imutável permite verificar autenticidade sem depender só do emissor.',
                'options' => [
                    ['text' => 'Para tornar o certificado editável por qualquer pessoa', 'is_correct' => false],
                    ['text' => 'Para permitir verificação pública de autenticidade', 'is_correct' => true],
                    ['text' => 'Para substituir a necessidade de estudar', 'is_correct' => false],
                    ['text' => 'Para ocultar que o aluno concluiu o curso', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'O que é "alucinação" em LLMs?',
                'explanation' => 'Alucinação é quando o modelo gera informação incorreta com tom confiante.',
                'options' => [
                    ['text' => 'Erro de hardware na GPU', 'is_correct' => false],
                    ['text' => 'Resposta plausível porém factualmente incorreta', 'is_correct' => true],
                    ['text' => 'Modo de economia de energia', 'is_correct' => false],
                    ['text' => 'Certificado inválido na blockchain', 'is_correct' => false],
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
