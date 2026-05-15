<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Code de reinitialisation du mot de passe',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '
                <div style="font-family: Arial, sans-serif; color: #16120d; line-height: 1.6;">
                    <h2 style="color: #8b5720;">SolarLight</h2>
                    <p>Voici votre code de reinitialisation du mot de passe :</p>
                    <p style="font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #c89742;">'.$this->code.'</p>
                    <p>Ce code expire dans 10 minutes.</p>
                    <p>Si vous n avez pas demande cette reinitialisation, ignorez simplement cet email.</p>
                </div>
            ',
        );
    }
}
