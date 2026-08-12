<?php

namespace App\Notifications;

use App\Models\AiInteraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AiTutorFallbackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AiInteraction $interaction,
    ) {}

    public function via(object $notifiable): array
    {
        // database evita spam de e-mail a cada pergunta em modo demo
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->interaction->course;

        return (new MailMessage)
            ->subject('Tutor de IA em modo demonstração — '.$course->title)
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Sua pergunta no curso **'.$course->title.'** foi atendida em modo demonstração.')
            ->line('Para respostas com o contexto completo das aulas, configure a chave da OpenAI no ambiente.')
            ->action('Abrir curso', url('/cursos/'.$course->slug))
            ->line('Continue estudando — o quiz e o certificado seguem disponíveis.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ai_tutor_fallback',
            'interaction_id' => $this->interaction->id,
            'course_id' => $this->interaction->course_id,
            'course_slug' => $this->interaction->course?->slug,
            'message' => 'Tutor de IA respondeu em modo demonstração.',
        ];
    }
}
