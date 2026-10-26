<?php

declare(strict_types=1);

namespace BuligAI\Shared\Supabase;

use RuntimeException;

final class ApiException extends RuntimeException
{
    public function __construct(public readonly int $status)
    {
        parent::__construct("Supabase request failed (HTTP {$status}).");
    }
}
