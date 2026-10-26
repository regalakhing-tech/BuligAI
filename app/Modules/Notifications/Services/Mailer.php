<?php

declare(strict_types=1);

namespace BuligAI\Modules\Notifications\Services;

use BuligAI\Shared\Mail\MailFactory;

final class Mailer
{
    public function __construct(private readonly array $config)
    {
    }

    public function send(string $recipient, string $subject, string $text): void
    {
        $mail = MailFactory::create($this->config);
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->Body = $text;
        $mail->isHTML(false);
        $mail->send();
    }
}
