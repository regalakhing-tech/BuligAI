<?php

declare(strict_types=1);

namespace BuligAI\Shared\Mail;

use InvalidArgumentException;
use PHPMailer\PHPMailer\PHPMailer;

final class MailFactory
{
    public static function create(array $config): PHPMailer
    {
        if (empty($config['host']) || !filter_var($config['from_address'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Configure MAIL_HOST and a valid MAIL_FROM_ADDRESS.');
        }
        if (!in_array($config['encryption'], ['tls', 'ssl'], true)) {
            throw new InvalidArgumentException('MAIL_ENCRYPTION must be tls or ssl.');
        }
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->Port = (int) $config['port'];
        $mail->SMTPAuth = $config['username'] !== '';
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->SMTPDebug = 0;
        $mail->Timeout = 15;
        $mail->setFrom($config['from_address'], $config['from_name']);
        return $mail;
    }
}
