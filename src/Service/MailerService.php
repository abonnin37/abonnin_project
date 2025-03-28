<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;
    private string $adminEmail;

    public function __construct(MailerInterface $mailer, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function sendEmail(string $to, string $subject, string $htmlContent): void
    {
        $email = (new Email())
            ->from($this->adminEmail)
            ->to($to)
            ->subject($subject)
            ->html($htmlContent);

        $this->mailer->send($email);
    }

    public function sendContactEmail(string $name, string $email, string $message): void
    {
        $subject = 'Nouveau message de contact';
        $htmlContent = "
            <h2>Nouveau message de contact</h2>
            <p><strong>Nom :</strong> {$name}</p>
            <p><strong>Email :</strong> {$email}</p>
            <p><strong>Message :</strong></p>
            <p>{$message}</p>
        ";

        $this->sendEmail($this->adminEmail, $subject, $htmlContent);
    }
} 