<?php

declare(strict_types=1);

namespace BuligAI\Shared\Security;

use InvalidArgumentException;

final class Csrf
{
    public static function token(array &$session): string
    {
        return $session['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function verify(array $session, string $submitted): void
    {
        if (empty($session['csrf']) || $submitted === '' || !hash_equals($session['csrf'], $submitted)) {
            throw new InvalidArgumentException('Your form expired. Go back, reload the page, and try again.');
        }
    }
}
