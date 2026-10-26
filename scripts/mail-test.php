<?php

declare(strict_types=1);

use BuligAI\Modules\Notifications\Services\Mailer;

require dirname(__DIR__) . '/bootstrap/app.php';

$recipient = $argv[1] ?? '';
if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: composer mail:test -- recipient@example.com\n");
    exit(1);
}
try {
    $config = require dirname(__DIR__) . '/config/app.php';
    (new Mailer($config['mail']))->send($recipient, 'BuligAI SMTP test', 'Your BuligAI SMTP configuration works.');
    echo "Test email sent.\n";
} catch (Throwable) {
    fwrite(STDERR, "Email could not be sent. Check your SMTP configuration.\n");
    exit(1);
}
