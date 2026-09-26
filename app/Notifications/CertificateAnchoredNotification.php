<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateAnchoredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Certificate $certificate,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->certificate->course?->title ?? 'seu curso';
        $simulated = $this->certificate->isSimulatedAnchor();

        $mail = (new MailMessage)
            ->subject(($simulated ? 'Certificado registrado (demo)' : 'Certificado ancorado na blockchain').' — '.$course)
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Seu certificado do curso **'.$course.'** foi registrado.');

        if ($simulated) {
            $mail->line('A ancoragem está em modo demonstração (mock). Em produção, o hash fica na rede pública.');
        } else {
            $mail->line('A transação na rede '.$this->certificate->blockchain_network.' foi confirmada.');
        }

        return $mail
            ->action('Ver certificado', route('certificates.show', $this->certificate))
            ->line('Você também pode compartilhar o link público de verificação.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certificate_anchored',
            'certificate_uuid' => $this->certificate->uuid,
            'course_id' => $this->certificate->course_id,
            'course_title' => $this->certificate->course?->title,
            'blockchain_network' => $this->certificate->blockchain_network,
            'blockchain_tx_hash' => $this->certificate->blockchain_tx_hash,
            'simulated' => $this->certificate->isSimulatedAnchor(),
            'verification_url' => $this->certificate->verificationUrl(),
            'message' => $this->certificate->isSimulatedAnchor()
                ? 'Certificado registrado em modo demonstração.'
                : 'Certificado ancorado na blockchain.',
        ];
    }
}
