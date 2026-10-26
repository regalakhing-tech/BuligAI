<?php

declare(strict_types=1);

namespace BuligAI\Tests\Unit;

use BuligAI\Shared\Mail\MailFactory;
use PHPUnit\Framework\TestCase;

final class MailFactoryTest extends TestCase
{
    public function testBuildsAuthenticatedTlsMailerWithoutSending(): void
    {
        $mail = MailFactory::create([
            'host' => 'smtp.example.com', 'port' => 587, 'username' => 'user', 'password' => 'secret',
            'encryption' => 'tls', 'from_address' => 'hello@example.com', 'from_name' => 'BuligAI',
        ]);
        self::assertSame('smtp', $mail->Mailer);
        self::assertSame('tls', $mail->SMTPSecure);
        self::assertTrue($mail->SMTPAuth);
        self::assertSame('utf-8', strtolower($mail->CharSet));
        self::assertSame('hello@example.com', $mail->From);
        self::assertSame(0, $mail->SMTPDebug);
    }
}
