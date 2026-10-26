<?php

declare(strict_types=1);

namespace BuligAI\Shared\Http;

final class View
{
    public static function render(string $template, array $data = []): string
    {
        $e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        extract($data, EXTR_SKIP);
        ob_start();
        require dirname(__DIR__, 2) . '/' . $template . '.php';
        $content = ob_get_clean();
        ob_start();
        require __DIR__ . '/../Views/layout.php';
        return ob_get_clean();
    }
}
