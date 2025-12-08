<?php

namespace App\Mail;

use App\Models\Orden;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdenEstadoCambio extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Orden $orden)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $estadoMap = [
            'recibida' => '📋 Tu orden ha sido recibida',
            'preparando' => '👨‍🍳 Tu orden se está preparando',
            'en_camino' => '🚚 Tu orden está en camino',
            'entregada' => '✅ Tu orden ha sido entregada',
            'cancelada' => '❌ Tu orden ha sido cancelada',
        ];

        $asunto = $estadoMap[$this->orden->estado] ?? 'Cambio de estado en tu orden';

        return new Envelope(
            subject: $asunto,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orden_estado_cambio',
            with: [
                'orden' => $this->orden,
                'cliente' => $this->orden->cliente,
                'restaurante' => $this->orden->restaurante,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
